<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// pricing',
    'title'   => 'Every environment is different. So is every quote.',
    'lead'    => 'We don\'t publish price lists that fit nobody. Each environment is scoped from its workload and requirements, and you receive a written quote before any work starts.',
    'ctas'    => true,
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <?php partial('components/pricing-model'); ?>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="split">
      <div class="prose">
        <p class="eyebrow">// how quotes work</p>
        <h2 class="section-title section-title--sm">What a quote contains</h2>
        <p>Scope, assumptions and exclusions written out plainly. For projects, a fixed price or a clearly bounded estimate. For ongoing work, a monthly fee with the services and response expectations it covers.</p>
        <p>We also estimate the infrastructure cost for your chosen provider, so you can see the total picture — even though that part is billed by the provider, not by us.</p>
      </div>
      <div class="prose">
        <p class="eyebrow">// what we need</p>
        <h2 class="section-title section-title--sm">To quote accurately</h2>
        <ul>
          <li>Approximate daily log volume and peak rate</li>
          <li>Retention requirements per type of log</li>
          <li>Number and type of log sources</li>
          <li>Preferred infrastructure, or constraints on it</li>
          <li>Availability expectations and maintenance windows</li>
          <li>Whether you want a handover or ongoing operations</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<?php partial('components/cta-band', ['title' => 'Request a quote', 'text' => 'Rough numbers are enough to start. We will ask for what is missing.']); ?>
