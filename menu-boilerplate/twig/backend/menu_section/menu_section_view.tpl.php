{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}{{ menu_section }} | Menu Sections{% endblock %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('fork-knife') ~ ' Menus', 'menu_index'),
    bootstrap_breadcrumb(menu_section.menu, 'menu_view', {id: menu_section.menu.id}),
    bootstrap_breadcrumb(menu_section),
  ) }}
{% endblock %}

{% set can_create_menu_item = is_granted(attributes.item.create, new_menu_item) %}

{% set create_menu_item_href = path('menu_item_create', {id: menu_section.id}) %}

{% block actions %}
  {% if can_create_menu_item %}
    <a href="{{ create_menu_item_href }}" class="btn btn-primary">
      {{ bootstrap_icon('plus-lg') }} Add Menu Item
    </a>
  {% endif %}

  {% if is_granted(attributes.section.edit, menu_section) %}
    <a class="btn btn-secondary" href="{{ path('menu_section_edit', {id: menu_section.id}) }}">
      {{ bootstrap_icon('pencil') }}
      Edit Menu Section
    </a>
  {% endif %}

  {% if is_granted(attributes.section.delete, menu_section) %}
    <a class="btn btn-danger" href="{{ path('menu_section_delete', {id: menu_section.id}) }}" data-confirm="Are you sure you want to delete this menu section? Clicking OK will take you to a verification step to delete this entry.">
      {{ bootstrap_icon('trash') }}
      Delete Menu Section
    </a>
  {% endif %}
{% endblock %}

{% set menu_item_count = menu_section.items|length %}

{% block main %}
<div class="card">
  <div class="card-body">
    <div class="card-title card-title-with-count">
      <h1 class="card-title-heading">Items</h1>
      <div class="card-title-count">
        {{ bootstrap_badge_primary(menu_item_count) }}
      </div>
    </div>
    <h2 class="card-subtitle mb-3 text-body-secondary h5">{{ menu_section }}</h2>

    {% if menu_item_count %}
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
          data-sortable-url="{{ path('menu_item_reorder_post') }}"
        >
          {% for menu_item in menu_section.items %}
            {{ _self.table_row(menu_item, attributes) }}
          {% endfor %}
        </tbody>
      </table>
    {% else %}
      <p>
        No menu items found.
        {% if can_create_menu_item %}
          <a href="{{ create_menu_item_href }}">
            Click here to add a menu item.
          </a>
        {% endif %}
      </p>
    {% endif %}
  </div>
</div>
{% endblock %}

{% macro table_row(menu_item, attributes) %}
  {% set row_actions = [] %}

  {% if is_granted(attributes.item.edit, menu_item) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_item_edit',
      route_params: {id: menu_item.id},
      color: 'secondary',
      icon: 'pencil',
      text: 'Edit Menu Item',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.item.delete, menu_item) %}
    {% set row_actions = row_actions|merge([{
      route: 'menu_item_delete',
      route_params: {id: menu_item.id},
      color: 'danger',
      icon: 'trash',
      text: 'Delete Menu Item',
      confirm: 'Are you sure you want to delete this menu item? Clicking OK will take you to a verification step to delete this entry.',
    }]) %}
  {% endif %}

  <tr data-id="{{ menu_item.id }}">
    <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
    <td>
      {% if menu_item.image %}
        <a href="{{ file_path(menu_item.image) }}" target="_blank" data-bypass>
          {{ image_tag(menu_item.image, {
            width: 50,
            height: 50,
          }) }}
        </a>
      {% endif %}
    </td>
    <td>
      {{ menu_item }}
      <br>
      {% if menu_item.isPublished %}
        {{ bootstrap_badge_success('Published') }}
      {% elseif menu_item.isScheduled %}
        {{ bootstrap_badge_warning('Scheduled') }}
      {% else %}
        {{ bootstrap_badge_secondary('Draft') }}
      {% endif %}
    </td>
    <td>
      {% if menu_item.prices.count == 1 %}
        <small class="d-block">${{ menu_item.prices[0].amount }}</small>
      {% else %}
        {% for price in menu_item.prices %}
          <small class="d-flex gap-1 justify-content-between">
            <b>{{ price.label }}</b>
            ${{ price.amount }}
          </small>
        {% endfor %}
      {% endif %}
    </td>
    <td>{{ menu_item.updatedAt|datetime }}</td>
    <td>
      {% include '@OHMediaBackend/widget/row_actions.html.twig' with {
        row_actions: row_actions,
      } only %}
    </td>
  </tr>
{% endmacro %}
