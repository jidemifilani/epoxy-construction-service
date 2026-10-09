</main>

<footer class="foot">
  <div class="foot__grid" aria-hidden="true"></div>
  <div class="wrap foot__cols">

    <div class="foot__col foot__col--brand">
      <a class="brand brand--light" href="<?= e(url()) ?>">
        <?= brand_mark() ?>
        <span class="brand__text"><strong><?= e(cfg('company_name')) ?></strong><small><?= e(cfg('tagline')) ?></small></span>
      </a>
      <p>Durable epoxy flooring for homes, businesses and designers across <?= e(cfg('service_area')) ?>.</p>
      <p class="foot__creds">Licensed &middot; Insured &middot; <?= e(cfg('warranty_years')) ?>-Year Warranty</p>
      <?php
      $social = array_filter(cfg('social', []));
      if (!empty($social)): ?>
      <ul class="foot__social">
        <?php foreach ($social as $name => $href): ?>
        <li><a href="<?= e($href) ?>" target="_blank" rel="noopener"><?= e($name) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>

    <div class="foot__col">
      <h2>Services</h2>
      <ul class="foot__links">
        <li><a href="<?= e(url('services/residential')) ?>">Residential Epoxy</a></li>
        <li><a href="<?= e(url('services/commercial')) ?>">Commercial &amp; Industrial</a></li>
        <li><a href="<?= e(url('services/decorative')) ?>">Decorative &amp; Metallic</a></li>
        <li><a href="<?= e(url('areas')) ?>">Areas we cover</a></li>
        <li><a href="<?= e(url('services/residential')) ?>#spaces">Garage Floors</a></li>
        <li><a href="<?= e(url('process')) ?>#step-4">Concrete Repair</a></li>
        <li><a href="<?= e(url('process')) ?>#step-3">Floor Removal &amp; Prep</a></li>
      </ul>
    </div>

    <div class="foot__col">
      <h2>Company</h2>
      <ul class="foot__links">
        <li><a href="<?= e(url('about')) ?>">About Us</a></li>
        <li><a href="<?= e(url('process')) ?>">Our Process</a></li>
        <li><a href="<?= e(url('portfolio')) ?>">Portfolio</a></li>
        <li><a href="<?= e(url('testimonials')) ?>">Testimonials</a></li>
        <li><a href="<?= e(url('blog')) ?>">Blog &amp; Resources</a></li>
        <li><a href="<?= e(url('faq')) ?>">FAQ</a></li>
        <li><a href="<?= e(url('careers')) ?>">Careers</a></li>
        <li><a href="<?= e(url('training')) ?>">Training</a></li>
      </ul>
    </div>

    <div class="foot__col">
      <h2>Contact</h2>
      <ul class="foot__contact">
        <li><?= icon('pin') ?><span><?= e(cfg('office_address')) ?></span></li>
        <li><?= icon('phone') ?><a href="<?= e(tel_link()) ?>"><?= e(cfg('phone')) ?></a></li>
        <li><?= icon('chat') ?><a href="<?= e(whatsapp_link()) ?>" target="_blank" rel="noopener">WhatsApp: <?= e(cfg('whatsapp')) ?></a></li>
        <li><?= icon('mail') ?><a href="mailto:<?= e(cfg('email')) ?>"><?= email_html(cfg('email')) ?></a></li>
        <li><?= icon('clock') ?><span><?= e(cfg('hours_short')) ?></span></li>
      </ul>
      <a class="btn btn--primary btn--sm" href="<?= e(url('quote')) ?>"><span>Get a Free Quote</span></a>
    </div>

  </div>

  <div class="wrap foot__bar">
    <p>&copy; <?= e(date('Y')) ?> <?= e(cfg('company_name')) ?>. All rights reserved.</p>
    <ul>
      <li><a href="<?= e(url('privacy')) ?>">Privacy Policy</a></li>
      <li><a href="<?= e(url('terms')) ?>">Terms of Service</a></li>
      <li><a href="<?= e(url('sitemap.xml')) ?>">Sitemap</a></li>
    </ul>
  </div>
</footer>

<?php if (!is_current('contact')): ?>
<a class="float-wa" href="<?= e(whatsapp_link('Hello ' . cfg('company_name') . ', I have a question about epoxy flooring.')) ?>"
   target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
  <?= icon('chat', 'float-wa__icon') ?>
  <span class="float-wa__label">WhatsApp us</span>
</a>
<?php endif; ?>

<button class="to-top" type="button" id="toTop" aria-label="Back to top">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
</button>

<!-- Sticky mobile action bar: the script's mobile conversion requirement. -->
<div class="mobile-bar">
  <a href="<?= e(tel_link()) ?>"><?= icon('phone') ?><span>Call</span></a>
  <a href="<?= e(whatsapp_link()) ?>" target="_blank" rel="noopener"><?= icon('chat') ?><span>WhatsApp</span></a>
  <a class="mobile-bar__cta" href="<?= e(url('quote')) ?>"><?= icon('doc') ?><span>Free Quote</span></a>
</div>

<script src="<?= e(asset('js/main.js')) ?>?v=1" defer></script>
</body>
</html>
