<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PAGERS/NMZ_PAGER.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php $pager->setSurroundCount(2); ?>
<nav aria-label="Paginación">
<ul class="pagination pagination-nmz justify-content-center flex-wrap mb-0 px-1">
    <?php if ($pager->hasPrevious()) : ?>
    <li class="page-item">
        <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Anterior">&laquo;</a>
    </li>
    <?php endif; ?>
    <?php foreach ($pager->links() as $link) : ?>
    <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
        <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
    </li>
    <?php endforeach; ?>
    <?php if ($pager->hasNext()) : ?>
    <li class="page-item">
        <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Siguiente">&raquo;</a>
    </li>
    <?php endif; ?>
</ul>
</nav>