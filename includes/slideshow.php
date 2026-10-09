<?php
/** Home page projects slideshow. */

declare(strict_types=1);

/**
 * Projects slideshow for the home page. Each slide shows the project's photo (or
 * its drawn scene if no photo is attached), its name, place and system, and a link
 * to the project. The first slide is visible; main.js moves the rest.
 * Without JavaScript the first project is shown and the link still works.
 */
function home_slideshow(array $projects): void
{
    $projects = array_values($projects);
    $total = count($projects);
    if ($total === 0) {
        return;
    }

    $arrowPrev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>';
    $arrowNext = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M9 6l6 6-6 6"/></svg>';

    echo '<div class="slides reveal" data-slides data-interval="6500" role="region" aria-roledescription="carousel" aria-label="Recent projects">';
    echo '<div class="slides__viewport">';
    foreach ($projects as $i => $project) {
        echo '<figure class="slide" role="group" aria-roledescription="slide" aria-label="' . ($i + 1) . ' of ' . $total . '"'
           . ($i === 0 ? '' : ' hidden') . '>';
        echo '<div class="slide__media">' . project_visual($project) . '</div>';
        echo '<figcaption class="slide__caption">';
        echo '<span class="tag tag--' . e($project['tag']) . '">' . e(ucfirst($project['tag'])) . '</span>';
        echo '<h3>' . e($project['name']) . '</h3>';
        echo '<p>' . e($project['location']) . ' &middot; ' . e($project['size']) . ' &middot; ' . e($project['system']) . '</p>';
        echo '<a class="link-arrow" href="' . e(url('portfolio')) . '#' . e($project['slug']) . '">View this project' . icon('arrow', 'link-arrow__icon') . '</a>';
        echo '</figcaption></figure>';
    }
    echo '</div>';

    echo '<button class="slides__btn slides__btn--prev" type="button" data-slide-prev aria-label="Previous project">' . $arrowPrev . '</button>';
    echo '<button class="slides__btn slides__btn--next" type="button" data-slide-next aria-label="Next project">' . $arrowNext . '</button>';

    echo '<div class="slides__dots" role="group" aria-label="Choose a project">';
    foreach ($projects as $i => $project) {
        echo '<button type="button" data-slide-go="' . $i . '" aria-label="Show ' . e($project['name']) . '"'
           . ($i === 0 ? ' aria-current="true"' : '') . '></button>';
    }
    echo '</div>';

    echo '<button class="slides__pause" type="button" data-slide-pause aria-pressed="false" aria-label="Pause slideshow"><span>Pause</span></button>';
    echo '</div>';
}
