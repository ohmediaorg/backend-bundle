{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}Menus{% endblock %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
  ) }}
{% endblock %}

{% set can_create_menu = is_granted(attributes.menu.create, new_menu) %}

{% set create_menu_href = path('menu_create') %}

{% block actions %}
  {% if can_create_menu %}
    <a href="{{ create_menu_href }}" class="btn btn-primary">
      {{ bootstrap_icon('plus-lg') }} Add Menu
    </a>
  {% endif %}
{% endblock %}

{% block main %}
  {% set menu_count = menus|length %}

  <div class="card">
    <div class="card-body">
      <div class="card-title card-title-with-count">
        <h1 class="card-title-heading">Menus</h1>
        <div class="card-title-count">
          {{ bootstrap_badge_primary(menu_count) }}
        </div>
      </div>

      {% if menu_count %}
        <table class="table table-striped">
          <thead>
            <tr>
              <th style="width:1rem">&nbsp;</th>
              <th>Name</th>
              <th>Sections</th>
              <th>Last Updated</th>
              <th></th>
            </tr>
          </thead>
          <tbody
            data-sortable
            data-sortable-csrf-name="{{ csrf_token_name }}"
            data-sortable-csrf-token="{{ csrf_token(csrf_token_name) }}"
            data-sortable-url="{{ path('menu_reorder_post') }}"
          >
            {% for menu in menus %}
              {{ _self.table_row(menu, attributes) }}
            {% endfor %}
          </tbody>
        </table>
      {% else %}
        <p>
          No menus found.
          {% if can_create_menu %}
            <a href="{{ create_menu_href }}">
              Click here to add a menu.
            </a>
          {% endif %}
        </p>
      {% endif %}
    </div>
  </div>
{% endblock %}

{% macro table_row(menu, attributes) %}
  {% set row_actions = [] %}

  {% if is_granted(attributes.menu.edit, menu) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_view',
      route_params: {id: menu.id},
      color: 'outline-dark',
      icon: 'eye',
      text: 'View Menu',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.menu.edit, menu) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_edit',
      route_params: {id: menu.id},
      color: 'secondary',
      icon: 'pencil',
      text: 'Edit Menu',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.menu.delete, menu) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_delete',
      route_params: {id: menu.id},
      color: 'danger',
      icon: 'trash',
      text: 'Delete Menu',
      confirm: 'Are you sure you want to delete this menu? Clicking OK will take you to a verification step to delete this entry.',
    }]) %}
  {% endif %}

  <tr data-id="{{ menu.id }}">
    <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
    <td>
      {{ menu }}
      <br>
      {% if menu.isPublished %}
        {{ bootstrap_badge_success('Published') }}
      {% elseif menu.isScheduled %}
        {{ bootstrap_badge_warning('Scheduled') }}
      {% else %}
        {{ bootstrap_badge_secondary('Draft') }}
      {% endif %}
    </td>
    <td>
      {% for section in menu.sections %}
        <small class="d-block">
          <a href="{{ path('menu_section_view', {id: section.id}) }}">
            {{ section }}
          </a>
        </small>
      {% endfor %}
    </td>
    <td>{{ menu.updatedAt|datetime }}</td>
    <td>
      {% include '@OHMediaBackend/widget/row_actions.html.twig' with {
        row_actions: row_actions,
      } only %}
    </td>
  </tr>
{% endmacro %}
