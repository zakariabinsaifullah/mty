# MTY Theme — Section Design Rules

Role: Read figma design, then make the exact design in WordPress (Use Figma MCP server and Verify each design using claude browser extension)
Follow these rules whenever you design or build a section, pattern, template or page for this theme.

> **CLAUDE.md wins over Figma (global rule):** the Figma design is not 100% consistent. Take content, structure and layout from Figma, but whenever a Figma style value (spacing, padding, gaps, radius, line-height, letter spacing, font size, colour…) differs from a rule in this file, **follow this file**. Mention the notable differences to the user after building, rather than asking first.

## 0. Project basics

-   **Theme:** MTY, a block (FSE) theme. Text domain `mty`; code prefix `mty` / `MTY` (`mty_` functions, `MTY_` constants, `mty/` block names, `mty-` CSS classes and preset slugs).
-   **Figma file:** `https://www.figma.com/design/OmYw1CMCx6FCYG0R7kdKfb/-MTY--More-2-You-Law-Zak?node-id=11098-4&p=f&t=6AEkPUzVoFI5BOIg-0`. Everything lives on the **↳ Full Design** page: one section per page (e.g. `[MTY] More 2 You Law - Homepage`, with desktop and mobile frames) and an `Assets • <page name>` section to its right, plus `Inner Pages • Components`, `Navigation States`, `Web Assets` and the `Brand Book`. The Figma colour variables (`Color/Goblin`, `Color/Green Pea`, …) and the type scale map to the `mty-*` presets in section 7.
-   **Repo:** https://github.com/zakariabinsaifullah/mty (public, default branch `main`).
-   **Build:** block and extension sources live in `src/`; run `npm run build` after changing anything there. `build/` is git-ignored (CI builds it).
-   **Deploy (push → live):** pushing to `main` runs `.github/workflows/deploy.yml`: it builds the theme, copies the production files (everything except what `.distignore` lists, so no `src/`, `node_modules/` or dev files, but with `build/`) to the `deploy` branch, then POSTs the `DEPLOYER_WEBHOOK_URL` secret. **Deployer for Git** on the live site has the `mty` theme linked to `https://github.com/zakariabinsaifullah/mty`, branch `deploy`, and reinstalls it from that branch when the webhook fires. Never point Deployer at `main` (it has no `build/`). Only push when the user asks. Check a deploy with `gh run list` / `gh run watch`; if the webhook step fails or is skipped, the theme can be redeployed by hand from Deployer for Git → Dashboard → **Deploy Theme**.
-   **Release:** pushing a tag runs `.github/workflows/release.yml`, which builds the theme, zips it as `mty.zip` (root folder `mty/`, same file list as the deploy branch) and publishes a GitHub Release using the `TOKEN` secret. Tags are the plain theme version, no `v` prefix (e.g. `0.2.1`). To release: bump `Version:` in `style.css` and `version` in `package.json`, commit, push `main`, then `git tag <version> && git push origin <version>`.
-   **Version:** the theme version lives in `style.css` (`Version:`); keep `package.json` in step.
-   **Repo secrets:** `TOKEN` (GitHub token for releases) and `DEPLOYER_WEBHOOK_URL` (the theme's webhook URL from Deployer for Git → Dashboard → "Show webhook URL"). Never print, commit or echo either value.
-   **Where to build designs (global rule):** when the user asks to design a section (or page) from Figma, always build it on the **live site** (https://mty.1wp.site), never on the local Studio site. Edit it in the live site's block editor through Claude in Chrome (the user is logged in there), and use the `wp-mty` MCP server (section 0a) for the content reads/writes it supports. Verify the result on the live front end. The image-upload rule in section 6 applies there too (BinsOptimizer is active on the live site). Theme code changes still happen locally and reach the live site by pushing to `main`.

## 0a. Live site MCP server (`wp-mty`)

-   **What it is:** the Viagent Production MCP server on the live site (`https://mty.1wp.site/wp-json/viagent-production/v1/mcp`), added to Claude Code as `wp-mty` with local scope (`claude mcp add --transport http wp-mty … --header "Authorization: Bearer …"`). The Bearer token lives only in `~/.claude.json`; never write it into this file, the repo or any page. Its tools are named `mcp__wp-mty__*` and load at session start.
-   **Use it for live content work:**
    -   Read: `get_site_info`, `list_posts`, `get_post`, `get_post_blocks`, `list_templates` / `get_template`, `list_patterns` / `get_pattern`, `get_menu`, `list_media`, `get_global_styles`.
    -   Write: `create_post` / `update_post` (block markup), `edit_post_blocks` (change single blocks by path instead of resending the page), `replace_in_post`, `update_template` (headers, footers, templates), `update_navigation_menu`, `update_media` (alt text, captions), `set_featured_image`.
    -   Check markup with `prepare_content` before saving it, and use `list_revisions` / `restore_revision` to undo a bad save.
    -   Every rule in this file still applies to markup written through the server (Kadence Row Layout container, spacing, presets, no custom names…).
-   **Don't use these tools, use the alternative:**
    -   `upload_media_from_url` / `upload_media_base64`: they skip BinsOptimizer. Upload images through the block editor instead (section 6).
    -   `write_extension_file` / `delete_extension_file` on the MTY theme: theme code is changed locally and reaches the live site by deploy (section 0). Editing it live makes local and live drift apart.
    -   `update_global_styles`: user global styles override `theme.json`. Change colours, typography and buttons in `theme.json` instead.
-   **Ask first:** anything destructive or site-wide (`delete_*`, plugin/theme install, activate, update or delete, users, `update_settings`, SEO settings, `robots.txt`, redirects, comments) needs the user's clear yes for that action.
-   **Always verify** a server write on the live front end with Claude in Chrome. A successful tool response only means the data was saved, not that the section looks right.

## 1. Container: Kadence Row Layout

-   Wrap every section in a **Kadence Row Layout** block (`kadence/rowlayout`).
-   Don't use `core/group`, `core/cover` or `core/columns` as the outer container of a section.
-   **Top-level width (global rule):** every top-level Row Layout (the section wrapper) is set to **Full Width** alignment (`"align":"full"`) with **Inherit Max Width from Theme?** turned on (`"inheritMaxWidth":true`), so its background spans the screen and its content follows the theme's 1310px content width. Never type a custom Max Width on it. Nested Row Layouts don't get this.
-   **Global rule:** don't give blocks custom names (no `"metadata":{"name":...}`). Leave every block with its default name in the List View; the user renames blocks themselves if needed.

## 2. Block priority

Pick blocks in this order, and move down a level only when the level above can't do the job:

1. **Core WordPress blocks.** These are strongly preferred for all content: headings, paragraphs, buttons, images, lists, query loops and so on.
2. **Custom blocks in the MTY inserter category** (`mty/*`):
    - Icon (`mty/icon`)
    - Social Share (`mty/social-share`)
3. Always use the MTY **Icon** block where an icon is used. Always use SVG code, and save the SVGs under **My Icons** for future use.
4. **Other Kadence blocks.** Use these only when neither core blocks nor the theme's custom blocks can do the job.

## 3. Row Layout spacing

-   A **standalone** Row Layout (a top-level section, not nested inside another Row Layout) uses:
    -   **100px** padding top and bottom
    -   **15px** padding left and right
-   In block markup that's `"padding":[100,15,100,15]`. The order is top, right, bottom, left.
-   on tablet use 70px top bottom padding (mobile will be auto like tablet)
-   **Global rule:** every top-level Row Layout has its top and bottom margin set to **0** (`"margin":[0,"",0,""],"marginUnit":"px"`). This applies everywhere: pages, posts, patterns, templates and template parts. Otherwise the theme's global block gap (1.2rem) adds about 19px between sections, on top of the padding.
-   Row Layouts nested inside another Row Layout don't get this default. Space them as the design needs.
-   but in case of same background based sections like two sections, both have same background, then use **Inner Space** 100px in total (50px-50px) while on tablet it will be (35px-35px)
-   **Section title group (global rule):** wrap a section's title and its description (if any) in a **Group** (`core/group`), and put the **50px** space below them on that Group as its bottom margin, never on the heading or on the last paragraph. The Group's **Block spacing is always 12px** (`blockGap: 12px`), whatever spacing Figma shows between title and description; per the block-spacing rule, **leave the title's and description's margins unset** (never 0), or the 12px won't apply. Group: `"style":{"spacing":{"blockGap":"12px","margin":{"top":"0","bottom":"50px"}}}`. This applies on every device, and also when there's only a title (the Group still carries the 50px).
-   Content width is **1310px**; wide width is **1440px** (the Figma frame width).
-   Inner padding for cards is **32px** by default; boxes that Figma pads more tightly (e.g. the 12px × 20px boxed section titles) keep the Figma padding
-   for 2 columns section, the columns gap is 70px where row gap is 50px
-   for 3 columns section, the columns gap is 24px and row gap is also 24px
-   on tablet and mobile all columns will have 1 column

## 3a. Card columns (nested Row Layout)

-   Build card grids as a nested Row Layout (`kadence/rowlayout`) with one **Section** (`kadence/column`) per card.
-   Columns gap is always **24px**, both between columns and between rows: `"columnGutter":"custom","customGutter":[24,24,24],"collapseGutter":"custom","customRowGutter":[24,24,24]`.
-   On tablet and mobile, cards are always **1 column**: `"tabletLayout":"row","mobileLayout":"row"`.
-   For image cards, don't use `core/cover`. Use the Section's own **Background Image + Overlay** settings (`backgroundImg`, `overlay` with a `mty-*` colour, `overlayOpacity`).
-   Cards in the same row are always **equal height**. If the design gives a card height, set it as the Section's min height. Kadence adds padding on top of min height, so enter the design height minus the top and bottom padding (for example, 443px card − 64px padding = `"height":[379]`).
-   Card content is vertically aligned to the **bottom** (`"verticalAlignment":"bottom"`).
-   Card inner padding is an equal **32px** on all sides (`"padding":[32,32,32,32]`).
-   Cards are **square** (no border radius), per the global radius rule in section 4.
-   **Content gap (global rule):** the gap between stacked content items (icon → heading → description) is always **24px**. Set it with the Section's **Vertical Gap** setting (`"rowGapVariable":["custom","",""],"rowGap":[24,"",""],"rowGapUnit":"px"`), not with block margins.
-   **Numbered items (global rule):** in a content item made of a number + heading + description (e.g. process steps "01 / 02 / 03"), the three are always **24px** apart (the content gap). Put them in one vertical Group with `blockGap: 24px`, leaving the items' margins unset (block-spacing rule); anything after the item (a divider, a button) sits outside that Group with its own gap.
-   **Block spacing: never set child margins (global rule):** when spacing comes from a core block's **Block spacing** (`blockGap` on a Group, Row, Stack, Buttons, etc.), **never set a margin on the blocks inside it — not even 0**. Leave headings, paragraphs, etc. with their margins unset. WordPress applies block spacing as each block's top margin (and clears the theme's default margins itself), so a block's own `"margin":{"top":"0"...}` overrides it and the spacing disappears.
-   **Kadence Section Vertical Gap means zero margins (global rule):** a Kadence Section's **Vertical Gap** is a real CSS gap that doesn't clear the theme's default margins, so there every block inside (headings, paragraphs, icons, lists, buttons, etc.) does get **0 top and bottom margin** (and 0 padding on text blocks): `"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}`. Otherwise the heading's 12px / paragraph's 16px bottom margin adds to the gap.

## 3b. Content columns (nested Row Layout)

-   For content layouts that aren't card grids, such as text beside an image, the column gap is **70px**: `"columnGutter":"custom","customGutter":[70,70,70]`.
-   When the columns stack on tablet and mobile, the row gap is **50px**: `"collapseGutter":"custom","customRowGutter":[50,50,50]`.
-   Card grids keep the 24px gap from section 3a.

## 4. Style with existing block settings

Style blocks through the options they already have, in this order:

1. **Block settings in the editor sidebar:** typography, colour, spacing, border, layout.
2. **The theme's presets from `theme.json`.** Never hard-code values that a preset already covers (see section 7 for the full set).
3. **Registered block styles:** Button — Alternative, Outline (core) and Link; Group — Wrap Mobile and Plain Links (links inside aren't underlined; use it for link lists like "On this page"). Links inherit their text's font, weight and line-height, so set the weight on the paragraph, not the link.
4. **The theme's editor extensions:**
    - Highlight (rich text format: the **Highlight** button with the "A" icon in its own group on the block toolbar, next to Bold/Italic; not core's "Highlight" in the More menu). Makes the selected words semi-bold (600) in `mty-goblin`; another MTY colour can be picked in its popover. Use it for the coloured words in headings (e.g. "Key **takeaways.**", "Meet **James.**"); never colour or bold those words by hand.
    - Hover Color
    - Text Max Width
    - Iconic Button (Icon Settings panel on a Button: add an SVG icon before/after the label)
    - Group Full Height, Group Overlay Background and Group Global Hover
    - Image Figure Frame (Image → Settings → **Figure Frame**; see section 6)
    - Pointer (Group and Kadence Section → Settings → **Pointer**): the Figma image "callouts", a 1px white line from the block's edge ending in a dot, pointing at the image beside it. Desktop only (hidden at 1024px and below), so nothing to do for mobile. Put it on the **text side** (e.g. the boxed title Group, or the text column), never on the image, so it stays level with the text. Settings: Direction (Right/Left, towards the image), Length (px, dot included), Vertical position (Middle, or px From top), Size (**Large**: 18px dot with a Green Pea ring, e.g. homepage "The claims we take on."; **Small**: 10px white dot, e.g. practice-page text blocks).
    - Kadence Featured Image Background (Row Layout) and Kadence Column Step Dot (Section → Advanced → "Add Step Dot" for the dot on numbered step cards: top edge on desktop, left edge on tablet/mobile; use the toggle, never type the `mty-step-dot` class by hand)

**Border radius (global rule):** the MTY design is square. **Images and buttons always have a 0 border radius**, and so do cards, groups/boxes with a background or border, videos, embeds and similar elements (leave the radius setting empty or 0; the theme already makes buttons and framed images square). The only rounded element is the Icon block's box (10px).

## 5. No custom CSS or JS without permission

-   **Never** add custom CSS or JavaScript without asking the user first and getting a clear yes. This covers theme files, block "Additional CSS", inline `style` attributes that go beyond block settings, `theme.json` `css` fields, and new scripts.
-   If a design can't be built with the rules above, stop. Explain what's missing and propose the CSS/JS change, then wait for approval.

## 6. Images, Icons, Content

-   use exact text used in figma design
-   each page's images are in the `Assets • <page name>` section to the right of that page's section on the Full Design page (e.g. `Assets • [MTY] More 2 You Law - Homepage`); take them from there
-   for Icons, always use SVG codes
-   **Uploading images (global rule):** the site uses **BinsOptimizer**, which converts images to WebP and compresses them **in the browser**, before the upload reaches the server. It hooks the block editor's `mediaUpload` setting. So:
    -   **Never** upload images with `studio wp media import`, the REST API directly, the `wp-mty` MCP upload tools (`upload_media_from_url`, `upload_media_base64`), or by copying files into `uploads/`. Those skip BinsOptimizer and leave JPEG/PNG files unconverted.
    -   Upload through the block editor in the browser (Claude in Chrome), using the wrapped `mediaUpload` function:
        1. Make the source file fetchable by the browser: on the local site, put it in a temporary folder under `wp-content/uploads/` (e.g. `claude-upload-tmp/`); on the live site, use a URL the editor page can `fetch` (the same site, or a host that allows cross-origin requests; test it first, because Figma asset URLs may be blocked by CORS).
        2. Open any post or page in the editor and wait until `wp.data.select('core/block-editor').getSettings().mediaUpload.__biioWrapped` is `true`.
        3. Fetch the file as a blob, wrap it in a `File` with the right name and type, and call `mediaUpload({ filesList: [file], allowedTypes: ['image'], onFileChange, onError })`. Use the returned `id` and `url` (they end in `.webp`) in the blocks.
        4. Delete the temporary folder afterwards.
    -   Check the result: the attachment's file should be `.webp` and it should have `_biio_savings` / `_biio_optimized_at` meta.
-   **Icon block defaults** (match the Figma card icons): a **20px** icon, white (`mty-white`), centred in a **45×45px** box with a `mty-goblin` background and **10px** radius. These are the block's defaults, so a new Icon block needs no extra settings and no padding. Set only the desktop size (`"sizes":{"Desktop":20},"iconSize":20`); tablet and mobile inherit it.
-   **Icon + title gap (global rule):** when an Icon block shows a title next to the icon, the gap between them is always **16px** (`"listGap":"16px"`). If you set it by script, also set `blockStyle["--list-gap"]` to `"16px"`: the block only recalculates `blockStyle` when it renders in the editor, so changing `listGap` alone can leave the old gap in the saved markup.
-   **Figure frame (global rule):** images in the Figma design use a "figure" style: a faint 1px border, a dark overlay, four white corner ticks and a "FIG. 02" label top left. Recreate it with the Image block's **Figure Frame** panel (Settings tab), never with custom markup or CSS:
    -   Turn on **Enable figure frame** and type the number from the design into **FIG value** (e.g. `02` shows "FIG. 02"). Leave the value empty if the design shows no label.
    -   Switch off **Border**, **Corner ticks** or **Dark overlay** when the design leaves them out. For example, the "Meet James" portrait shows only the label.
    -   Framed images are square, like every image (section 4).
    -   Only core Image blocks have the panel. Images used as Kadence Section backgrounds can't be framed.
-   never create custom image without permission

## 7. Color, Typography

-   always use MTY color and typography presets
-   never use custom color and typography without permission
-   never use letter spacing without permission
-   **never use a custom line-height.** Headings and body text always use the theme's line-heights (1.2 and 1.5), even if the Figma value differs.
-   **Never set a custom bottom margin on headings.** All headings get a **12px** bottom margin (and 0 top margin) from the theme (`style.css`); paragraphs get 16px. The only exception is a Kadence Section's Vertical Gap (section 3a), where headings are set to 0 margin; inside a core block's Block spacing, leave heading margins unset.
-   **h1 and h2 always use the theme's default font size** (`mty-heading-one` / `mty-heading-two`). Never set a custom or different font size on an h1 or h2; if the design shows another size, pick the heading level that matches it instead.

**Colours** (`mty-*`, from the MTY Figma colour list; each family runs `-lightest`, `-lighter`, `-light`, base, `-dark`, `-darker`, `-darkest`):

| Role                                  | Preset                                                                       | Value     |
| ------------------------------------- | ---------------------------------------------------------------------------- | --------- |
| Primary / buttons / icon boxes        | `mty-goblin`                                                                 | `#3A8D3B` |
| Button hover                          | `mty-goblin-dark`                                                            | `#2E702F` |
| Secondary green                       | `mty-green-pea`                                                              | `#1E7444` |
| Site background (body)                | `mty-background` (Figma `Bg-1`)                                              | `#060606` |
| Default text: headings, body, links   | `mty-white`                                                                  | `#FFFFFF` |
| Dark backgrounds / text on light bg   | `mty-thunder`                                                                | `#231F20` |
| Muted text on light backgrounds       | `mty-neutral-dark`                                                           | `#555555` |
| Light backgrounds                     | `mty-neutral-lightest`, `mty-goblin-lightest`, `mty-green-pea-lightest`      |           |
| Neutrals                              | `mty-white`, `mty-white-*`, `mty-neutral-*`                                  |           |
| Transparent overlays                  | `mty-white-5` … `-60`, `mty-neutral-darkest-5` … `-60`, `mty-transparent`    |           |

The site is **dark by default**: the body background is `mty-background` (#060606) and all text defaults to white. On a light section (white, `mty-neutral-lightest`, `mty-goblin-lightest`…) set the heading and text colours to `mty-thunder` / `mty-neutral-dark` on the block.

**Typography:**

-   **Headings (h1–h6):** Alexandria, weight **300 (Light)**, line-height **1.2**, colour `mty-white`. Coloured, semi-bold words in a heading ("Key **takeaways.**") use the Highlight format, never a manual weight or colour.
-   **Body and paragraphs:** Open Sans, **16px** (`mty-regular`), weight **400**, line-height **1.5**, colour `mty-white`. Links are white too and turn `mty-goblin` on hover.
-   **Heading and large text sizes are fluid** (mobile → desktop, scaling between 375px and 1440px viewports):

| Preset              | Element      | Size      |
| ------------------- | ------------ | --------- |
| `mty-heading-one`   | h1           | 44 → 72px |
| `mty-heading-two`   | h2           | 40 → 52px |
| `mty-heading-three` | h3           | 32 → 44px |
| `mty-heading-four`  | h4           | 24 → 36px |
| `mty-heading-five`  | h5           | 20 → 28px |
| `mty-heading-six`   | h6           | 18 → 22px |
| `mty-large`         | Text Large   | 18 → 22px |
| `mty-medium`        | Text Medium  | 16 → 18px |

-   **Fixed text sizes:** `mty-regular` 16px (body), `mty-small` 14px, `mty-x-small` 12px.

**Buttons** (core Button block):

-   **Default (primary):** `mty-goblin` background, white text, padding 16px 28px, **square corners (0 radius)**, Open Sans 14px (`mty-small`) / 600 / 1.3, **uppercase**, letter spacing 0.12em (1.68px), centred. A **→ arrow** follows the label automatically (Open Sans 16px / 400, 12px gap); no icon setting needed. Hover darkens to `mty-goblin-dark`; text stays white. (Figma: `Button / Primary`.)
-   **Outline:** same as default but transparent with a 1px `currentColor` border, white text (for dark or image backgrounds), padding 15px 27px so it matches the default's size, and the same automatic → arrow. Hover turns text and border `mty-goblin`. (Figma: `Button / Outline`.)
-   The button's uppercase and letter spacing are part of the theme's button style, the one allowed exception to the "no letter spacing" rule; don't add them again on individual buttons.
-   All buttons transition over 0.3s `cubic-bezier(0.4, 0, 0.2, 1)`.
-   **Button icons:** default and outline buttons already show the → arrow. Only when the design shows a different icon, turn on **Icon Settings → Add Icon to Button** and paste the SVG code (save it to My Icons); that icon replaces the arrow. It follows the text colour, sizes to the text (1em) and sits **10px** from the label; leave Size/Gap empty unless the design differs.
