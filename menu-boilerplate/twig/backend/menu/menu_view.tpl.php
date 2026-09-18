{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}{{ menu }} | Menus{% endblock %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', '<?php echo $singular['snake_case']; ?>_index'),
    bootstrap_breadcrumb(menu),
  ) }}
{% endblock %}

{% set can_create_<?php echo $singular['snake_case']; ?>_section = is_granted(attributes.section.create, new_<?php echo $singular['snake_case']; ?>_section) %}

{% set create_<?php echo $singular['snake_case']; ?>_section_href = path('<?php echo $singular['snake_case']; ?>_section_create', {id: menu.id}) %}

{% block actions %}
  {% if can_create_<?php echo $singular['snake_case']; ?>_section %}
    <a href="{{ create_<?php echo $singular['snake_case']; ?>_section_href }}" class="btn btn-primary">
      {{ bootstrap_icon('plus-lg') }} Add Menu Section
    </a>
  {% endif %}

  {% if is_granted(attributes.menu.edit, menu) %}
    <a class="btn btn-secondary" href="{{ path('<?php echo $singular['snake_case']; ?>_edit', {id: menu.id}) }}">
      {{ bootstrap_icon('pencil') }}
      Edit Menu
    </a>
  {% endif %}

  {% if is_granted(attributes.menu.delete, menu) %}
    <a class="btn btn-danger" href="{{ path('<?php echo $singular['snake_case']; ?>_delete', {id: menu.id}) }}" data-confirm="Are you sure you want to delete this menu? Clicking OK will take you to a verification step to delete this entry.">
      {{ bootstrap_icon('trash') }}
      Delete Menu
    </a>
  {% endif %}
{% endblock %}

{% set <?php echo $singular['snake_case']; ?>_section_count = menu.sections|length %}

{% block main %}
<div class="card">
  <div class="card-body">
    <div class="card-title card-title-with-count">
      <h1 class="card-title-heading">Sections</h1>
      <div class="card-title-count">
        {{ bootstrap_badge_primary(<?php echo $singular['snake_case']; ?>_section_count) }}
      </div>
    </div>
    <h2 class="card-subtitle mb-3 text-body-secondary h5">{{ menu }}</h2>

    {% if <?php echo $singular['snake_case']; ?>_section_count %}
      <table class="table table-striped">
        <thead>
          <tr>
            <th style="width:1rem">&nbsp;</th>
            <th>Name</th>
            <th># Items</th>
            <th>Last Updated</th>
            <th></th>
          </tr>
        </thead>
        <tbody
          data-sortable
          data-sortable-csrf-name="{{ csrf_token_name }}"
          data-sortable-csrf-token="{{ csrf_token(csrf_token_name) }}"
          data-sortable-url="{{ path('<?php echo $singular['snake_case']; ?>_section_reorder_post') }}"
        >
          {% for <?php echo $singular['snake_case']; ?>_section in menu.sections %}
            {{ _self.table_row(<?php echo $singular['snake_case']; ?>_section, attributes) }}
          {% endfor %}
        </tbody>
      </table>
    {% else %}
      <p>
        No menu sections found.
        {% if can_create_<?php echo $singular['snake_case']; ?>_section %}
          <a href="{{ create_<?php echo $singular['snake_case']; ?>_section_href }}">
            Click here to add a menu section.
          </a>
        {% endif %}
      </p>
    {% endif %}
  </div>
</div>
{% endblock %}

{% macro table_row(<?php echo $singular['snake_case']; ?>_section, attributes) %}
  {% set row_actions = [] %}

  {% if is_granted(attributes.section.edit, <?php echo $singular['snake_case']; ?>_section) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_section_view',
      route_params: {id: <?php echo $singular['snake_case']; ?>_section.id},
      color: 'outline-dark',
      icon: 'eye',
      text: 'View Menu Section',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.section.edit, <?php echo $singular['snake_case']; ?>_section) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_section_edit',
      route_params: {id: <?php echo $singular['snake_case']; ?>_section.id},
      color: 'secondary',
      icon: 'pencil',
      text: 'Edit Menu Section',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.section.delete, <?php echo $singular['snake_case']; ?>_section) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_section_delete',
      route_params: {id: <?php echo $singular['snake_case']; ?>_section.id},
      color: 'danger',
      icon: 'trash',
      text: 'Delete Menu Section',
      confirm: 'Are you sure you want to delete this menu section? Clicking OK will take you to a verification step to delete this entry.',
    }]) %}
  {% endif %}

  <tr data-id="{{ <?php echo $singular['snake_case']; ?>_section.id }}">
    <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
    <td>
      {{ <?php echo $singular['snake_case']; ?>_section }}
      <br>
      {% if <?php echo $singular['snake_case']; ?>_section.isPublished %}
        {{ bootstrap_badge_success('Published') }}
      {% elseif <?php echo $singular['snake_case']; ?>_section.isScheduled %}
        {{ bootstrap_badge_warning('Scheduled') }}
      {% else %}
        {{ bootstrap_badge_secondary('Draft') }}
      {% endif %}
    </td>
    <td>{{ <?php echo $singular['snake_case']; ?>_section.items.count }}</td>
    <td>{{ <?php echo $singular['snake_case']; ?>_section.updatedAt|datetime }}</td>
    <td>
      {% include '@OHMediaBackend/widget/row_actions.html.twig' with {
        row_actions: row_actions,
      } only %}
    </td>
  </tr>
{% endmacro %}
