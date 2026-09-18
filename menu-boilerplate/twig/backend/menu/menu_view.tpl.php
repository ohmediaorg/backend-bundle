{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}{{ menu }} | Menus{% endblock %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
    bootstrap_breadcrumb(menu),
  ) }}
{% endblock %}

{% set can_create_menu_section = is_granted(attributes.section.create, new_menu_section) %}

{% set create_menu_section_href = path('menu_section_create', {id: menu.id}) %}

{% block actions %}
  {% if can_create_menu_section %}
    <a href="{{ create_menu_section_href }}" class="btn btn-primary">
      {{ bootstrap_icon('plus-lg') }} Add Menu Section
    </a>
  {% endif %}

  {% if is_granted(attributes.menu.edit, menu) %}
    <a class="btn btn-secondary" href="{{ path('menu_edit', {id: menu.id}) }}">
      {{ bootstrap_icon('pencil') }}
      Edit Menu
    </a>
  {% endif %}

  {% if is_granted(attributes.menu.delete, menu) %}
    <a class="btn btn-danger" href="{{ path('menu_delete', {id: menu.id}) }}" data-confirm="Are you sure you want to delete this menu? Clicking OK will take you to a verification step to delete this entry.">
      {{ bootstrap_icon('trash') }}
      Delete Menu
    </a>
  {% endif %}
{% endblock %}

{% set menu_section_count = menu.sections|length %}

{% block main %}
<div class="card">
  <div class="card-body">
    <div class="card-title card-title-with-count">
      <h1 class="card-title-heading">Sections</h1>
      <div class="card-title-count">
        {{ bootstrap_badge_primary(menu_section_count) }}
      </div>
    </div>
    <h2 class="card-subtitle mb-3 text-body-secondary h5">{{ menu }}</h2>

    {% if menu_section_count %}
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
          data-sortable-url="{{ path('menu_section_reorder_post') }}"
        >
          {% for menu_section in menu.sections %}
            {{ _self.table_row(menu_section, attributes) }}
          {% endfor %}
        </tbody>
      </table>
    {% else %}
      <p>
        No menu sections found.
        {% if can_create_menu_section %}
          <a href="{{ create_menu_section_href }}">
            Click here to add a menu section.
          </a>
        {% endif %}
      </p>
    {% endif %}
  </div>
</div>
{% endblock %}

{% macro table_row(menu_section, attributes) %}
  {% set row_actions = [] %}

  {% if is_granted(attributes.section.edit, menu_section) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_section_view',
      route_params: {id: menu_section.id},
      color: 'outline-dark',
      icon: 'eye',
      text: 'View Menu Section',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.section.edit, menu_section) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_section_edit',
      route_params: {id: menu_section.id},
      color: 'secondary',
      icon: 'pencil',
      text: 'Edit Menu Section',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.section.delete, menu_section) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_section_delete',
      route_params: {id: menu_section.id},
      color: 'danger',
      icon: 'trash',
      text: 'Delete Menu Section',
      confirm: 'Are you sure you want to delete this menu section? Clicking OK will take you to a verification step to delete this entry.',
    }]) %}
  {% endif %}

  <tr data-id="{{ menu_section.id }}">
    <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
    <td>
      {{ menu_section }}
      <br>
      {% if menu_section.isPublished %}
        {{ bootstrap_badge_success('Published') }}
      {% elseif menu_section.isScheduled %}
        {{ bootstrap_badge_warning('Scheduled') }}
      {% else %}
        {{ bootstrap_badge_secondary('Draft') }}
      {% endif %}
    </td>
    <td>{{ menu_section.items.count }}</td>
    <td>{{ menu_section.updatedAt|datetime }}</td>
    <td>
      {% include '@OHMediaBackend/widget/row_actions.html.twig' with {
        row_actions: row_actions,
      } only %}
    </td>
  </tr>
{% endmacro %}
