<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * Migas en mayúsculas (estilo INICIO / SECCIÓN) + h1 alineado con el hero de Retratos.
 *
 * @var array<int, array{label: string, url?: string|null}> $nmzHeroCrumbs  último ítem sin url = actual
 * @var string                                               $nmzHeroTitle
 * @var string                                               $nmzHeroCrumbsClass clases extra en <nav>
 */
$nmzHeroCrumbs      = $nmzHeroCrumbs ?? [];
$nmzHeroTitle       = (string) ($nmzHeroTitle ?? '');
$nmzHeroCrumbsClass = trim((string) ($nmzHeroCrumbsClass ?? ''));
?>
<?php if ($nmzHeroCrumbs !== []) : ?>
<nav class="nmz-hero-crumbs<?= $nmzHeroCrumbsClass !== '' ? ' ' . esc($nmzHeroCrumbsClass, 'attr') : '' ?>" aria-label="<?= esc('Migas de pan', 'attr') ?>">
    <ol class="nmz-hero-crumbs__list">
        <?php foreach ($nmzHeroCrumbs as $c) :
            $label = trim((string) ($c['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $url    = array_key_exists('url', $c) ? $c['url'] : null;
            $hasUrl = $url !== null && $url !== '';
            ?>
        <li class="nmz-hero-crumbs__item">
            <?php if ($hasUrl) : ?>
            <a href="<?= esc((string) $url, 'attr') ?>"><?= esc($label) ?></a>
            <?php else : ?>
            <span aria-current="page"><?= esc($label) ?></span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
<?php if ($nmzHeroTitle !== '') : ?>
<h1 class="nmz-page-hero__title"><?= esc($nmzHeroTitle) ?></h1>
<?php endif; ?>
