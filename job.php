<?php
/**
 * JOB - one posting and its apply form. Reached as /careers/<slug> through
 * the rewrite in .htaccess.
 */

require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug'], '/') : '';
$job  = $slug === '' ? null : job_find($slug);

if ($job === null) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$isOpen = $job['status'] === 'open';
$errors = [];
$old    = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!$isOpen) {
        // A direct repost to a closed role is refused, even though the form is hidden.
        flash('error', 'This position is no longer open.');
        header('Location: ' . url('careers/' . $job['slug']), true, 303);
        exit;
    }

    $old = $_POST;
    $guard = submission_guard_failed();
    if ($guard !== null) {
        $errors['_form'] = $guard;
    }

    if ($errors === []) {
        [$data, $errors] = validate_application();
    }

    $resumePath = $originalName = $mime = '';
    if ($errors === []) {
        try {
            [$resumePath, $originalName, $mime] = resume_store_upload($_FILES['resume'] ?? []);
        } catch (InvalidArgumentException $e) {
            $errors['resume'] = $e->getMessage();
        } catch (Throwable $e) {
            error_log('[iris] resume upload failed: ' . $e->getMessage());
            $errors['_form'] = 'Your CV could not be saved. Please try again.';
        }
    }

    if ($errors === []) {
        try {
            $applicationId = db_insert('job_applications', [
                'job_id'                => $job['id'],
                'job_title'              => $job['title'],
                'applicant_name'         => $data['applicant_name'],
                'email'                  => $data['email'],
                'phone'                  => $data['phone'],
                'cover_note'             => $data['cover_note'],
                'resume_path'            => $resumePath,
                'resume_original_name'   => $originalName,
                'resume_mime'            => $mime,
                'ip_address'             => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]);
        } catch (Throwable $e) {
            error_log('[iris] could not save application: ' . $e->getMessage());
            $errors['_form'] = 'We could not save your application. Please try again, or email ' . cfg('email') . '.';
        }
    }

    if ($errors === []) {
        notify_owner_whatsapp(
            'job application',
            $data['applicant_name'],
            $data['phone'],
            whatsapp_detail_line([$job['title'], $data['email']]),
            'Application #' . $applicationId
        );

        start_session_if_needed();
        $_SESSION['last_submission'] = time();
        $_SESSION['lead_kind'] = 'application';
        $_SESSION['lead_name'] = $data['applicant_name'];
        header('Location: ' . url('thank-you'), true, 303);
        exit;
    }
}

$responsibilities = job_lines($job['responsibilities']);
$requirements     = job_lines($job['requirements']);

$pageTitle       = $job['title'] . ' | Careers at ' . cfg('company_name');
$pageDescription = $job['summary'];
$pageKeywords    = $job['title'] . ' job Lagos, ' . strtolower($job['department']) . ' job ' . cfg('city') . ', careers ' . cfg('company_name');
$noindex         = !$isOpen;

$bannerEyebrow = 'Careers';
$bannerTitle   = e($job['title']);
$bannerLede    = $job['summary'];
$trail         = ['Careers' => 'careers', $job['title'] => null];

$schemaNodes = $isOpen ? [schema_job_posting($job)] : [];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap split split--wide" style="align-items:start">

    <article class="prose">
      <dl class="spec" style="max-width:420px;margin-bottom:1.6rem">
        <div class="spec__row"><dt>Department</dt><dd><?= e($job['department']) ?></dd></div>
        <div class="spec__row"><dt>Location</dt><dd><?= e($job['location']) ?></dd></div>
        <div class="spec__row"><dt>Type</dt><dd><?= e($job['employment_type']) ?></dd></div>
        <?php if (trim((string) $job['salary_range']) !== ''): ?>
        <div class="spec__row"><dt>Pay</dt><dd><?= e($job['salary_range']) ?></dd></div>
        <?php endif; ?>
        <div class="spec__row"><dt>Status</dt><dd><?= $isOpen ? 'Open' : 'Filled' ?></dd></div>
      </dl>

      <?php if ($responsibilities !== []): ?>
      <h2>What you will do</h2>
      <?php tick_list(array_map('e', $responsibilities)); ?>
      <?php endif; ?>

      <?php if ($requirements !== []): ?>
      <h2>What we are looking for</h2>
      <?php tick_list(array_map('e', $requirements)); ?>
      <?php endif; ?>

      <?php if (!$isOpen): ?>
      <div class="note reveal" style="margin-top:1.6rem">
        <strong>This position has been filled.</strong> See the other
        <a href="<?= e(url('careers')) ?>">open roles</a>, or send a speculative CV to
        <a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a>.
      </div>
      <?php endif; ?>
    </article>

    <?php if ($isOpen): ?>
    <div class="form-card reveal" id="apply" style="scroll-margin-top:110px">
      <h2 style="font-size:1.4rem">Apply for This Role</h2>
      <p class="field__hint" style="margin-bottom:1rem">We reply to every applicant we shortlist. Your CV is kept private and is never shown on the website.</p>

      <?php if (!empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
      <?php endif; ?>

      <form class="form" method="post" action="<?= e(url('careers/' . $job['slug'])) ?>#apply" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>
        <div class="honeypot" aria-hidden="true">
          <label for="website">Website</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="field">
          <label for="applicant_name">Your name <span class="req">*</span></label>
          <input type="text" id="applicant_name" name="applicant_name" required autocomplete="name"
                 value="<?= e(old($old, 'applicant_name')) ?>" <?= isset($errors['applicant_name']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'applicant_name') ?>
        </div>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="email">Email <span class="req">*</span></label>
            <input type="email" id="email" name="email" required autocomplete="email"
                   value="<?= e(old($old, 'email')) ?>" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'email') ?>
          </div>
          <div class="field">
            <label for="phone">Phone / WhatsApp <span class="req">*</span></label>
            <input type="tel" id="phone" name="phone" required autocomplete="tel"
                   value="<?= e(old($old, 'phone')) ?>" <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'phone') ?>
          </div>
        </div>

        <div class="field">
          <label for="resume">Your CV <span class="req">*</span></label>
          <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required
                 <?= isset($errors['resume']) ? 'aria-invalid="true"' : '' ?>>
          <small class="field__hint">PDF or Word document, up to 5 MB.</small>
          <?= field_error($errors, 'resume') ?>
        </div>

        <div class="field">
          <label for="cover_note">Anything you would like to add? <span class="field__hint">(optional)</span></label>
          <textarea id="cover_note" name="cover_note" placeholder="Relevant experience, availability, anything that is not on your CV"><?= e(old($old, 'cover_note')) ?></textarea>
          <?= field_error($errors, 'cover_note') ?>
        </div>

        <button class="btn btn--primary btn--lg" type="submit" style="justify-content:center"><span>Submit Application</span></button>
        <p class="field__hint">We never share your details. See our <a href="<?= e(url('privacy')) ?>">privacy policy</a>.</p>
      </form>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
