<?php
/**
 * Blog article page. Reached as /blog/<slug> via the rewrite in .htaccess.
 *
 * An article with text in its Article text field is published: the text is
 * rendered, the page is indexable and it gets Article structured data.
 *
 * An article without text is a planned brief. It still renders (so the URL and
 * internal links can be tested) but it is marked noindex, so search engines do
 * not index thin pages.
 */

require_once __DIR__ . '/includes/functions.php';

/**
 * Turns the article text into HTML. Everything is escaped first, so the only
 * markup that appears is what this function adds: paragraphs, lists, bold.
 */
function article_body_html(string $text): string
{
    $inline = static function (string $line): string {
        return preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e($line)) ?? e($line);
    };

    $html = '';
    $paragraph = [];
    $list = null;   // 'ul' | 'ol' | null

    $flushParagraph = static function () use (&$html, &$paragraph, $inline): void {
        if ($paragraph !== []) {
            $html .= '<p>' . implode(' ', array_map($inline, $paragraph)) . '</p>' . "\n";
            $paragraph = [];
        }
    };
    $closeList = static function () use (&$html, &$list): void {
        if ($list !== null) {
            $html .= '</' . $list . '>' . "\n";
            $list = null;
        }
    };

    foreach (explode("\n", str_replace("\r", '', $text)) as $raw) {
        $line = trim($raw);
        if ($line === '') {
            $flushParagraph();
            $closeList();
        } elseif (preg_match('/^-\s+(.*)$/', $line, $m)) {
            $flushParagraph();
            if ($list !== 'ul') { $closeList(); $html .= '<ul>' . "\n"; $list = 'ul'; }
            $html .= '<li>' . $inline($m[1]) . '</li>' . "\n";
        } elseif (preg_match('/^\d+\.\s+(.*)$/', $line, $m)) {
            $flushParagraph();
            if ($list !== 'ol') { $closeList(); $html .= '<ol>' . "\n"; $list = 'ol'; }
            $html .= '<li>' . $inline($m[1]) . '</li>' . "\n";
        } else {
            $closeList();
            $paragraph[] = $line;
        }
    }
    $flushParagraph();
    $closeList();
    return $html;
}

$slug    = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug'], '/') : '';
$article = $slug === '' ? null : find_article($slug);

if ($article === null) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

[$slug, $title, $category, $purpose, $outline, $body, $publishedAt] = $article;
$isPublished = trim($body) !== '';

// Titles stay under about 60 characters so search results do not cut them off.
$pageTitle       = mb_strlen($title) > 50 ? $title : $title . ' | ' . cfg('company_short');
$pageDescription = $purpose;
$seoType         = $isPublished ? 'article' : 'website';
$noindex         = !$isPublished;

$bannerEyebrow = $category;
$bannerTitle   = e($title);
$trail         = ['Blog' => 'blog', $category => null];

$schemaNodes = $isPublished
    ? [schema_article($title, $purpose, 'blog/' . $slug, $publishedAt !== '' ? date('c', strtotime($publishedAt)) : date('c'))]
    : [];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap split split--wide" style="align-items:start">
    <article class="prose">
      <?php if ($isPublished): ?>
        <p class="article-lede reveal"><?= e($purpose) ?></p>
        <div class="article-body reveal"><?= article_body_html($body) ?></div>
      <?php else: ?>
        <p class="note reveal">
          <strong>Coming soon.</strong> This article is being written. It is live now so the page address and
          links can be checked, but it is hidden from search engines until the text is published.
        </p>
        <h2>What this article is for</h2>
        <p><?= e($purpose) ?></p>
        <h2>Planned sections</h2>
        <ol class="reveal">
          <?php foreach ($outline as $section): ?>
          <li><?= e($section) ?></li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <div class="article-cta reveal">
        <p><strong>Want the number for your floor?</strong> Book a free site inspection. We measure, test the slab
          and send a line-itemed quote within 48 hours.</p>
        <div class="cta-row"><?= button('Request My Free Quote', url('quote'), 'primary') ?></div>
      </div>

      <h2>Related</h2>
      <ul class="reveal">
        <li><a href="<?= e(url('process')) ?>">The seven-step installation process</a></li>
        <li><a href="<?= e(url('services/residential')) ?>#costs">Residential pricing</a></li>
        <li><a href="<?= e(url('services/commercial')) ?>">Commercial and industrial systems</a></li>
        <li><a href="<?= e(url('faq')) ?>">Epoxy flooring FAQ</a></li>
        <li><a href="<?= e(url('shop')) ?>">Buy epoxy resin and primer</a></li>
      </ul>
    </article>

    <aside class="rail">
      <div class="rail__card reveal">
        <h3>In this guide</h3>
        <p class="field__hint"><?= e($category) ?></p>
        <?php if ($isPublished && $outline !== []): ?>
        <ul class="foot__links" style="margin:.6rem 0 0">
          <?php foreach ($outline as $section): ?><li><?= e($section) ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div class="rail__card reveal">
        <h3>More guides</h3>
        <ul class="foot__links" style="margin:0">
          <?php
          $others = array_values(array_filter(articles(), static fn (array $a): bool => $a[0] !== $slug));
          foreach (array_slice($others, 0, 5) as $other) {
              echo '<li><a href="' . e(url('blog/' . $other[0])) . '">' . e($other[1]) . '</a></li>';
          }
          ?>
          <li><a href="<?= e(url('blog')) ?>">All guides</a></li>
        </ul>
      </div>
    </aside>
  </div>
</section>

<?php
cta_band(
    'Get a Free Quote on Your Project',
    'Free on-site inspection and a fixed-price written quote within 48 hours.',
    'Request My Free Quote'
);
?>

<?php require __DIR__ . '/includes/footer.php'; ?>
