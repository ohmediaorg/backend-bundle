{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}{{ <?php echo $singular['snake_case']; ?>_section }} | Menu Sections{% endblock %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $singular['title_case']; ?>s', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section.menu, '<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>_section.menu.id}),
    bootstrap_breadcrumb(<?php echo $singular['snake_case']; ?>_section),
  ) }}
{% endblock %}

{% set can_create_<?php echo $singular['snake_case']; ?>_item = is_granted(attributes.item.create, new_<?php echo $singular['snake_case']; ?>_item) %}

{% set create_<?php echo $singular['snake_case']; ?>_item_href = path('<?php echo $singular['snake_case']; ?>_item_create', {id: <?php echo $singular['snake_case']; ?>_section.id}) %}

{% block actions %}
  {% if can_create_<?php echo $singular['snake_case']; ?>_item %}
    <a href="{{ create_<?php echo $singular['snake_case']; ?>_item_href }}" class="btn btn-primary">
      {{ bootstrap_icon('plus-lg') }} Add Menu Item
    </a>
  {% endif %}

  {% if is_granted(attributes.section.edit, <?php echo $singular['snake_case']; ?>_section) %}
    <a class="btn btn-secondary" href="{{ path('<?php echo $singular['snake_case']; ?>_section_edit', {id: <?php echo $singular['snake_case']; ?>_section.id}) }}">
      {{ bootstrap_icon('pencil') }}
      Edit Menu Section
    </a>
  {% endif %}

  {% if is_granted(attributes.section.delete, <?php echo $singular['snake_case']; ?>_section) %}
    <a class="btn btn-danger" href="{{ path('<?php echo $singular['snake_case']; ?>_section_delete', {id: <?php echo $singular['snake_case']; ?>_section.id}) }}" data-confirm="Are you sure you want to delete this menu section? Clicking OK will take you to a verification step to delete this entry.">
      {{ bootstrap_icon('trash') }}
      Delete Menu Section
    </a>
  {% endif %}
{% endblock %}

{% set <?php echo $singular['snake_case']; ?>_item_count = <?php echo $singular['snake_case']; ?>_section.items|length %}

{% block main %}
<div class="card">
  <div class="card-body">
    <div class="card-title card-title-with-count">
      <h1 class="card-title-heading">Items</h1>
      <div class="card-title-count">
        {{ bootstrap_badge_primary(<?php echo $singular['snake_case']; ?>_item_count) }}
      </div>
    </div>
    <h2 class="card-subtitle mb-3 text-body-secondary h5">{{ <?php echo $singular['snake_case']; ?>_section }}</h2>

    {% if <?php echo $singular['snake_case']; ?>_item_count %}
      <table class="table table-striped">
        <thead>
          <tr>
            <th style="width:1rem">&nbsp;</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Last Updated</th>
            <th></th>
          </tr>
        </thead>
        <tbody
          data-sortable
          data-sortable-csrf-name="{{ csrf_token_name }}"
          data-sortable-csrf-token="{{ csrf_token(csrf_token_name) }}"
          data-sortable-url="{{ path('<?php echo $singular['snake_case']; ?>_item_reorder_post') }}"
        >
          {% for <?php echo $singular['snake_case']; ?>_item in <?php echo $singular['snake_case']; ?>_section.items %}
            {{ _self.table_row(<?php echo $singular['snake_case']; ?>_item, attributes) }}
          {% endfor %}
        </tbody>
      </table>
    {% else %}
      <p>
        No menu items found.
        {% if can_create_<?php echo $singular['snake_case']; ?>_item %}
          <a href="{{ create_<?php echo $singular['snake_case']; ?>_item_href }}">
            Click here to add a menu item.
          </a>
        {% endif %}
      </p>
    {% endif %}
  </div>
</div>
{% endblock %}

{% macro table_row(<?php echo $singular['snake_case']; ?>_item, attributes) %}
  {% set row_actions = [] %}

  {% if is_granted(attributes.item.edit, <?php echo $singular['snake_case']; ?>_item) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_item_edit',
      route_params: {id: <?php echo $singular['snake_case']; ?>_item.id},
      color: 'secondary',
      icon: 'pencil',
      text: 'Edit Menu Item',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.item.delete, <?php echo $singular['snake_case']; ?>_item) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_item_delete',
      route_params: {id: <?php echo $singular['snake_case']; ?>_item.id},
      color: 'danger',
      icon: 'trash',
      text: 'Delete Menu Item',
      confirm: 'Are you sure you want to delete this menu item? Clicking OK will take you to a verification step to delete this entry.',
    }]) %}
  {% endif %}

  <tr data-id="{{ <?php echo $singular['snake_case']; ?>_item.id }}">
    <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
    <td>
      {% if <?php echo $singular['snake_case']; ?>_item.image %}
        <a href="{{ file_path(<?php echo $singular['snake_case']; ?>_item.image) }}" target="_blank" data-bypass>
          {{ image_tag(<?php echo $singular['snake_case']; ?>_item.image, {
            width: 50,
            height: 50,
          }) }}
        </a>
      {% endif %}
    </td>
    <td>
      {{ <?php echo $singular['snake_case']; ?>_item }}
      <br>
      {% if <?php echo $singular['snake_case']; ?>_item.isPublished %}
        {{ bootstrap_badge_success('Published') }}
      {% elseif <?php echo $singular['snake_case']; ?>_item.isScheduled %}
        {{ bootstrap_badge_warning('Scheduled') }}
      {% else %}
        {{ bootstrap_badge_secondary('Draft') }}
      {% endif %}
    </td>
    <td>
      {% if <?php echo $singular['snake_case']; ?>_item.prices.count == 1 %}
        <small class="d-block">${{ <?php echo $singular['snake_case']; ?>_item.prices[0].amount }}</small>
      {% else %}
        {% for price in <?php echo $singular['snake_case']; ?>_item.prices %}
          <small class="d-flex gap-1 justify-content-between">
            <b>{{ price.label }}</b>
            ${{ price.amount }}
          </small>
        {% endfor %}
      {% endif %}
    </td>
    <td>{{ <?php echo $singular['snake_case']; ?>_item.updatedAt|datetime }}</td>
    <td>
      {% include '@OHMediaBackend/widget/row_actions.html.twig' with {
        row_actions: row_actions,
      } only %}
    </td>
  </tr>
{% endmacro %}
