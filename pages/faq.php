<?php declare(strict_types=1);

$groups = data('faq');
$all = [];
foreach ($groups as $g) {
    foreach ($g as $f) {
        $all[] = $f;
    }
}
$page['schema'][] = schema_faq($all);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// faq',
    'title'   => 'Graylog questions, answered by engineers',
    'lead'    => 'Deployment, clusters, OpenSearch and Data Node, operations and licensing. If your question is not here, ask us directly.',
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="faq-page">
      <nav class="faq-page__toc mono" aria-label="FAQ categories">
        <ul>
          <?php foreach (array_keys($groups) as $name): ?>
            <li><a href="#<?= e('cat-' . strtolower(preg_replace('/[^a-z]+/i', '-', $name))) ?>"><?= e(strtolower($name)) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <div>
        <?php foreach ($groups as $name => $items): $id = 'cat-' . strtolower(preg_replace('/[^a-z]+/i', '-', $name)); ?>
          <div class="faq-group" id="<?= e($id) ?>">
            <h2 class="faq-group__title"><?= e($name) ?></h2>
            <?php partial('components/faq-list', ['items' => $items, 'idPrefix' => $id]); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php partial('components/cta-band'); ?>
