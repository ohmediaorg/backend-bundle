{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}<?php echo $plural['title']; ?>{% endblock %}

{% block breadcrumbs %}
  {{ bootstrap_breadcrumbs(
    bootstrap_breadcrumb(bootstrap_icon('<?php echo $icon; ?>') ~ ' <?php echo $plural['title']; ?>', '<?php echo $singular['snake_case']; ?>_index'),
  ) }}
{% endblock %}

{% set can_create_<?php echo $singular['snake_case']; ?> = is_granted(attributes.create, new_<?php echo $singular['snake_case']; ?>) %}

{% set create_<?php echo $singular['snake_case']; ?>_href = path('<?php echo $singular['snake_case']; ?>_create') %}

{% block actions %}
  {% if can_create_<?php echo $singular['snake_case']; ?> %}
    <a href="{{ create_<?php echo $singular['snake_case']; ?>_href }}" class="btn btn-primary">
      {{ bootstrap_icon('plus-lg') }}
      Add <?php echo $singular['title']."\n"; ?>
    </a>
  {% endif %}
{% endblock %}

{% block main %}
<?php if ($has_reorder) { ?>
  {% set <?php echo $singular['snake_case']; ?>_count = <?php echo $plural['snake_case']; ?>|length %}
<?php } else { ?>
  {% set <?php echo $singular['snake_case']; ?>_count = pagination.count %}
<?php } ?>

  <div class="card">
    <div class="card-body">
      <div class="card-title card-title-with-count">
        <h1 class="card-title-heading"><?php echo $plural['title']; ?></h1>
        <div class="card-title-count">
          {{ bootstrap_badge_primary(<?php echo $singular['snake_case']; ?>_count) }}
        </div>
      </div>
<?php if (!$has_reorder) { ?>

      {{ form_start(search_form) }}
        <div class="row align-items-end">
          <div class="col-lg-4 col-sm-6">
            {{ form_row(search_form.search) }}
          </div>
<?php if ($is_publishable) { ?>
          <div class="col-lg-4 col-sm-6">
            {{ form_row(search_form.status) }}
          </div>
<?php } ?>
          <div class="col-lg-12 col-sm-6">
            <button class="btn btn-outline-dark mb-3" type="submit">Search</button>
            <a class="btn btn-secondary mb-3" href="{{ path('<?php echo $singular['snake_case']; ?>_index') }}">Reset</a>
          </div>
        </div>
      {{ form_end(search_form) }}
<?php } ?>

      {% if <?php echo $singular['snake_case']; ?>_count %}
        <table class="table table-striped">
          <thead>
            <tr>
<?php if ($has_reorder) { ?>
              <th style="width:1rem">&nbsp;</th>
<?php } ?>
              <th><?php echo $singular['title']; ?></th>
              <th>Last Updated</th>
              <th></th>
            </tr>
          </thead>
<?php if ($has_reorder) { ?>
          <tbody
            data-sortable
            data-sortable-csrf-name="{{ csrf_token_name }}"
            data-sortable-csrf-token="{{ csrf_token(csrf_token_name) }}"
            data-sortable-url="{{ path('<?php echo $singular['snake_case']; ?>_reorder_post') }}"
          >
            {% for <?php echo $singular['snake_case']; ?> in <?php echo $plural['snake_case']; ?> %}
              {{ _self.table_row(<?php echo $singular['snake_case']; ?>, attributes) }}
            {% endfor %}
          </tbody>
<?php } else { ?>
          <tbody>
            {% for <?php echo $singular['snake_case']; ?> in pagination.results %}
              {{ _self.table_row(<?php echo $singular['snake_case']; ?>, attributes) }}
            {% endfor %}
          </tbody>
<?php } ?>
        </table>
<?php if (!$has_reorder) { ?>

        {{ bootstrap_pagination(pagination) }}

        <small>{{ bootstrap_pagination_info(pagination) }}</small>
<?php } ?>
      {% else %}
        <p>
          No <?php echo $plural['readable']; ?> found.
          {% if can_create_<?php echo $singular['snake_case']; ?> %}
            <a href="{{ create_<?php echo $singular['snake_case']; ?>_href }}">
              Click here to add <?php echo $determiner; ?> <?php echo $singular['readable']; ?>.
            </a>
          {% endif %}
        </p>
      {% endif %}
    </div>
  </div>
{% endblock %}

{% macro table_row(<?php echo $singular['snake_case']; ?>, attributes) %}
  {% set row_actions = [] %}

<?php if ($has_view_route) { ?>
  {% if is_granted(attributes.view, <?php echo $singular['snake_case']; ?>) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_view',
      route_params: {id: <?php echo $singular['snake_case']; ?>.id},
      color: 'outline-dark',
      icon: 'eye',
      text: 'View <?php echo $singular['title']; ?>',
    }]) %}
  {% endif %}

<?php } ?>
  {% if is_granted(attributes.edit, <?php echo $singular['snake_case']; ?>) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_edit',
      route_params: {id: <?php echo $singular['snake_case']; ?>.id},
      color: 'secondary',
      icon: 'pen',
      text: 'Edit <?php echo $singular['title']; ?>',
    }]) %}
  {% endif %}

  {% if is_granted(attributes.delete, <?php echo $singular['snake_case']; ?>) %}
    {% set row_actions = row_actions|merge([{
      route: '<?php echo $singular['snake_case']; ?>_delete',
      route_params: {id: <?php echo $singular['snake_case']; ?>.id},
      color: 'danger',
      icon: 'trash',
      text: 'Delete <?php echo $singular['title']; ?>',
      confirm: 'Are you sure you want to delete this <?php echo $singular['readable']; ?>? Clicking OK will take you to a verification step to delete this entry.',
    }]) %}
  {% endif %}

<?php if ($has_reorder) { ?>
  <tr data-id="{{ <?php echo $singular['snake_case']; ?>.id }}">
    <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
<?php } else { ?>
  <tr>
<?php } ?>
<?php if ($is_publishable) { ?>
    <td>
      {{ <?php echo $singular['snake_case']; ?> }}
      <br>
      {% if <?php echo $singular['snake_case']; ?>.isPublished %}
        {{ bootstrap_badge_success('Published') }}
      {% elseif <?php echo $singular['snake_case']; ?>.isScheduled %}
        {{ bootstrap_badge_warning('Scheduled') }}
      {% else %}
        {{ bootstrap_badge_secondary('Draft') }}
      {% endif %}
    </td>
<?php } else { ?>
    <td>{{ <?php echo $singular['snake_case']; ?> }}</td>
<?php } ?>
    <td>{{ <?php echo $singular['snake_case']; ?>.updatedAt|datetime }}</td>
    <td>
      {% include '@OHMediaBackend/widget/row_actions.html.twig' with {
        row_actions: row_actions,
      } only %}
    </td>
  </tr>
{% endmacro %}
