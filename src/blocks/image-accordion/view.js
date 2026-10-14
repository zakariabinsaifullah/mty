document.addEventListener('DOMContentLoaded', function () {
    const accordions = document.querySelectorAll('.wp-block-mty-image-accordion');

    accordions.forEach(function (accordion) {
        const items = accordion.querySelectorAll('.wp-block-mty-image-accordion-item');

        items.forEach(function (item, index) {
            if (index === 0) {
                item.classList.add('active');
            }

            item.addEventListener('click', function () {
                items.forEach(function (sibling) {
                    sibling.classList.remove('active');
                });
                item.classList.add('active');
            });
        });
    });
});
