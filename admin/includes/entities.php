<?php
/**
 * The admin sections, declared once. The generic list, edit and delete pages
 * are built from these definitions, so adding a field here adds it to the
 * form, the validation and the list in one step.
 *
 * Field options:
 *   type      text | textarea | slug | number | decimal | select | bool
 *   label     shown in the form and list column header
 *   required  true to refuse an empty value
 *   list      true to show the column in the list view
 *   options   for select: value => label
 *   help      a line of guidance under the control
 *   max       longest allowed text, in characters
 *   rows      for textarea
 *   format    list display: 'money' | 'bool' | 'label' (select label)
 */

return [

    'projects' => [
        'label'    => 'Projects',
        'singular' => 'project',
        'table'    => 'projects',
        'order'    => 'sort, id',
        'search'   => ['name', 'location', 'system'],
        'fields'   => [
            'name'      => ['type' => 'text', 'label' => 'Project name', 'required' => true, 'list' => true, 'max' => 160],
            'slug'      => ['type' => 'slug', 'label' => 'URL slug', 'required' => true, 'list' => false,
                            'help' => 'Lowercase, dashes only. Used for the anchor on the portfolio, e.g. lekki-house.'],
            'tag'       => ['type' => 'select', 'label' => 'Type', 'required' => true, 'list' => true, 'format' => 'label',
                            'options' => ['residential' => 'Residential', 'commercial' => 'Commercial', 'decorative' => 'Decorative']],
            'location'  => ['type' => 'text', 'label' => 'Location', 'list' => true, 'max' => 160],
            'size'      => ['type' => 'text', 'label' => 'Size', 'list' => true, 'max' => 40, 'help' => 'e.g. 180 m2'],
            'space'     => ['type' => 'text', 'label' => 'Space', 'max' => 160, 'help' => 'e.g. Living, dining and kitchen'],
            'system'    => ['type' => 'text', 'label' => 'System installed', 'max' => 200],
            'finish'    => ['type' => 'text', 'label' => 'Finish', 'max' => 200],
            'timeline'  => ['type' => 'text', 'label' => 'Timeline', 'max' => 80, 'help' => 'e.g. 4 days'],
            'art'       => ['type' => 'select', 'label' => 'Picture style', 'required' => true,
                            'options' => ['metallic' => 'Metallic flow', 'warehouse' => 'Warehouse lines', 'showroom' => 'Showroom',
                                          'kitchen' => 'Kitchen', 'entry' => 'Entry arch', 'garage' => 'Garage']],
            'summary'   => ['type' => 'textarea', 'label' => 'Summary', 'rows' => 4, 'max' => 800,
                            'help' => 'Shown on the card. Two or three sentences work best.'],
            'sort'      => ['type' => 'number', 'label' => 'Order', 'list' => false, 'help' => 'Lower numbers appear first.'],
            'published' => ['type' => 'bool', 'label' => 'Show on the site', 'list' => true, 'format' => 'bool'],
        ],
    ],

    'testimonials' => [
        'label'    => 'Testimonials',
        'singular' => 'testimonial',
        'table'    => 'testimonials',
        'order'    => 'sort, id',
        'search'   => ['name', 'quote', 'project'],
        'fields'   => [
            'quote'     => ['type' => 'textarea', 'label' => 'Quote', 'required' => true, 'rows' => 4, 'max' => 800],
            'name'      => ['type' => 'text', 'label' => 'Name', 'required' => true, 'list' => true, 'max' => 120],
            'role'      => ['type' => 'text', 'label' => 'Role or location', 'list' => true, 'max' => 160],
            'group_key' => ['type' => 'select', 'label' => 'Audience', 'required' => true, 'list' => true, 'format' => 'label',
                            'options' => ['homeowners' => 'Homeowners', 'businesses' => 'Businesses', 'trade' => 'Trade & architects']],
            'project'   => ['type' => 'text', 'label' => 'Project detail', 'max' => 200, 'help' => 'Type, size and system, e.g. Garage, 32 m2, flake system'],
            'sort'      => ['type' => 'number', 'label' => 'Order', 'help' => 'Lower numbers appear first.'],
            'published' => ['type' => 'bool', 'label' => 'Show on the site', 'list' => true, 'format' => 'bool'],
        ],
    ],

    'faqs' => [
        'label'    => 'FAQs',
        'singular' => 'question',
        'table'    => 'faqs',
        'order'    => 'group_sort, group_name, sort, id',
        'search'   => ['question', 'answer', 'group_name'],
        'fields'   => [
            'group_name'   => ['type' => 'text', 'label' => 'Group', 'required' => true, 'list' => true, 'max' => 120,
                               'help' => 'Questions with the same group are shown together, e.g. Pricing.'],
            'group_sort'   => ['type' => 'number', 'label' => 'Group order', 'help' => 'Orders the groups on the FAQ page.'],
            'question'     => ['type' => 'text', 'label' => 'Question', 'required' => true, 'list' => true, 'max' => 255],
            'answer'       => ['type' => 'textarea', 'label' => 'Answer', 'required' => true, 'rows' => 4, 'max' => 2000],
            'show_on_home' => ['type' => 'bool', 'label' => 'Show on the home page', 'list' => true, 'format' => 'bool'],
            'sort'         => ['type' => 'number', 'label' => 'Order within group'],
            'published'    => ['type' => 'bool', 'label' => 'Show on the site', 'list' => true, 'format' => 'bool'],
        ],
    ],

    'products' => [
        'label'    => 'Products',
        'singular' => 'product',
        'table'    => 'products',
        'order'    => 'channel, sort, id',
        'search'   => ['name', 'pack_label'],
        'fields'   => [
            'name'        => ['type' => 'text', 'label' => 'Product name', 'required' => true, 'list' => true, 'max' => 160,
                              'help' => 'Use the same name for every pack size, so the shop groups them together.'],
            'channel'     => ['type' => 'select', 'label' => 'Sold as', 'required' => true, 'list' => true, 'format' => 'label',
                              'options' => ['retail' => 'Retail (1 kg and up)', 'wholesale' => 'Wholesale (drums)']],
            'pack_label'  => ['type' => 'text', 'label' => 'Pack', 'required' => true, 'list' => true, 'max' => 80,
                              'help' => 'e.g. 1 kg tin, 20 kg pail, Drum (200 kg)'],
            'unit_price'  => ['type' => 'decimal', 'label' => 'Price per pack (naira)', 'required' => true, 'list' => true, 'format' => 'money'],
            'min_qty'     => ['type' => 'number', 'label' => 'Minimum packs per order', 'required' => true, 'min' => 1],
            'description' => ['type' => 'textarea', 'label' => 'Description', 'rows' => 3, 'max' => 600],
            'in_stock'    => ['type' => 'bool', 'label' => 'In stock', 'list' => true, 'format' => 'bool',
                              'help' => 'Untick when the pack is out of stock. Customers can still see it but cannot order it.'],
            'sort'        => ['type' => 'number', 'label' => 'Order', 'help' => 'Lower numbers appear first.'],
            'published'   => ['type' => 'bool', 'label' => 'Show in the shop', 'list' => true, 'format' => 'bool'],
        ],
    ],

    'seo_pages' => [
        'label'    => 'SEO by page',
        'singular' => 'page',
        'table'    => 'seo_pages',
        'order'    => 'path',
        'search'   => ['path', 'title'],
        'fields'   => [
            'path'        => ['type' => 'path', 'label' => 'Page address', 'list' => true,
                              'help' => 'e.g. services/residential. Leave empty for the home page.'],
            'title'       => ['type' => 'text', 'label' => 'Search title', 'list' => true, 'max' => 190,
                              'help' => 'Aim for 50-60 characters. Include the service and the city, e.g. Garage Epoxy Floor Lagos, Nigeria | Iris Epoxy.'],
            'description' => ['type' => 'textarea', 'label' => 'Search description', 'rows' => 3, 'max' => 400,
                              'help' => 'Aim for 140-158 characters. Say what the page offers and why to click it.'],
            'keywords'    => ['type' => 'text', 'label' => 'Keywords (optional)', 'max' => 400,
                              'help' => 'Search engines mostly ignore this. Used for your own notes.'],
            'noindex'     => ['type' => 'bool', 'label' => 'Hide this page from search engines', 'list' => true, 'format' => 'bool'],
        ],
    ],

    'jobs' => [
        'label'    => 'Job postings',
        'singular' => 'job',
        'table'    => 'jobs',
        'order'    => 'sort, id',
        'search'   => ['title', 'department', 'location'],
        'fields'   => [
            'title'            => ['type' => 'text', 'label' => 'Job title', 'required' => true, 'list' => true, 'max' => 160],
            'slug'             => ['type' => 'slug', 'label' => 'URL slug', 'required' => true,
                                    'help' => 'The page address is /careers/<slug>.'],
            'department'       => ['type' => 'text', 'label' => 'Department', 'list' => true, 'max' => 80],
            'location'         => ['type' => 'text', 'label' => 'Location', 'max' => 160],
            'employment_type'  => ['type' => 'select', 'label' => 'Type', 'list' => true, 'format' => 'label',
                                    'options' => ['Full-time' => 'Full-time', 'Part-time' => 'Part-time', 'Contract' => 'Contract', 'Internship' => 'Internship']],
            'salary_range'     => ['type' => 'text', 'label' => 'Pay (optional)', 'max' => 120,
                                    'help' => 'Only shown if you fill it in, e.g. N180,000 - N220,000 per month. Leave empty to say nothing about pay.'],
            'summary'          => ['type' => 'textarea', 'label' => 'Summary', 'required' => true, 'rows' => 3, 'max' => 600],
            'responsibilities' => ['type' => 'textarea', 'label' => 'Responsibilities', 'rows' => 8, 'max' => 4000, 'help' => 'One per line.'],
            'requirements'     => ['type' => 'textarea', 'label' => 'Requirements', 'rows' => 8, 'max' => 4000, 'help' => 'One per line.'],
            'status'           => ['type' => 'select', 'label' => 'Status', 'required' => true, 'list' => true, 'format' => 'label',
                                    'options' => ['open' => 'Open', 'closed' => 'Filled / closed']],
            'sort'             => ['type' => 'number', 'label' => 'Order', 'help' => 'Lower numbers appear first.'],
            'published'        => ['type' => 'bool', 'label' => 'Show on the site', 'list' => true, 'format' => 'bool'],
        ],
    ],

    'articles' => [
        'label'    => 'Blog articles',
        'singular' => 'article',
        'table'    => 'articles',
        'order'    => 'sort, id',
        'search'   => ['title', 'category'],
        'fields'   => [
            'title'     => ['type' => 'text', 'label' => 'Title', 'required' => true, 'list' => true, 'max' => 255],
            'slug'      => ['type' => 'slug', 'label' => 'URL slug', 'required' => true,
                            'help' => 'The page address is /blog/<slug>.'],
            'category'  => ['type' => 'text', 'label' => 'Category', 'list' => true, 'max' => 60, 'help' => 'e.g. Pricing, Decorative'],
            'purpose'   => ['type' => 'textarea', 'label' => 'Purpose', 'rows' => 3, 'max' => 800,
                            'help' => 'What the article is for. Shown on the blog index.'],
            'outline'   => ['type' => 'textarea', 'label' => 'Section outline', 'rows' => 6, 'max' => 4000,
                            'help' => 'One section per line.'],
            'body'      => ['type' => 'textarea', 'label' => 'Article text', 'rows' => 18, 'max' => 40000,
                            'help' => 'Leave empty to keep the article out of search results until it is written. Blank line between paragraphs. **bold** works. Start a line with - for bullets or 1. for numbered steps.'],
            'sort'      => ['type' => 'number', 'label' => 'Order'],
            'published' => ['type' => 'bool', 'label' => 'Publish', 'list' => true, 'format' => 'bool'],
        ],
    ],
];
