<style>
:root {
    /* Parvus Lightbox colors */
    --parvus-btn-color: <?= htmlspecialchars($site->parvusButtonColor()->or('var(--color-secondary)')) ?>;
    --parvus-btn-background-color: <?= htmlspecialchars($site->parvusButtonBackgroundColor()->or('transparent')) ?>;
    --parvus-btn-disabled-color: <?= htmlspecialchars($site->parvusButtonDisabledColor()->or('var(--color-fourth)')) ?>;
    --parvus-btn-disabled-background-color: <?= htmlspecialchars($site->parvusButtonDisabledBackgroundColor()->or('transparent')) ?>;
    --parvus-btn-hover-color: <?= htmlspecialchars($site->parvusButtonHoverColor()->or('var(--color-secondary)')) ?>;
    --parvus-btn-hover-background-color: <?= htmlspecialchars($site->parvusButtonHoverBackgroundColor()->or('transparent')) ?>;
    --parvus-background-color: <?= htmlspecialchars($site->parvusBackgroundColor()->or('var(--color-primary)')) ?>;
    --parvus-caption-color: <?= htmlspecialchars($site->parvusCaptionColor()->or('var(--color-fourth)')) ?>;

    /* Swiper colors */
    --swiper-navigation-color: <?= htmlspecialchars($site->swiperNavigationColor()->or('var(--color-primary)')) ?>;
    --swiper-pagination-color: <?= htmlspecialchars($site->swiperPaginationColor()->or('var(--color-primary)')) ?>;
    --swiper-pagination-bullet-inactive-color: <?= htmlspecialchars($site->swiperPaginationBulletInactiveColor()->or('var(--color-primary)')) ?>;
}
</style>