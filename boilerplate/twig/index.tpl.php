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
    <a href="{{ create_<?php echo $singular['snake_case']; ?>_href }}" class="btn btn-sm btn-primary">
      {{ bootstrap_icon('plus') }} Add <?php echo $singular['title']."\n"; ?>
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
            <tr data-id="{{ <?php echo $singular['snake_case']; ?>.id }}">
              <td data-handle>{{ bootstrap_icon('arrows-move') }}</td>
<?php } else { ?>
          <tbody>
            {% for <?php echo $singular['snake_case']; ?> in pagination.results %}
            <tr>
<?php } ?>
              <td>{{ <?php echo $singular['snake_case']; ?> }}</td>
              <td>{{ <?php echo $singular['snake_case']; ?>.updatedAt|datetime }}</td>
              <td>
<?php if ($has_view_route) { ?>
                {% if is_granted(attributes.view, <?php echo $singular['snake_case']; ?>) %}
                  <a class="btn btn-sm btn-primary btn-action" href="{{ path('<?php echo $singular['snake_case']; ?>_view', {id: <?php echo $singular['snake_case']; ?>.id}) }}" title="View">
                    {{ bootstrap_icon('eye-fill') }}
                    <span class="visually-hidden">View</span>
                  </a>
                {% endif %}
<?php } ?>
                {% if is_granted(attributes.edit, <?php echo $singular['snake_case']; ?>) %}
                  <a class="btn btn-sm btn-primary btn-action" href="{{ path('<?php echo $singular['snake_case']; ?>_edit', {id: <?php echo $singular['snake_case']; ?>.id}) }}" title="Edit">
                    {{ bootstrap_icon('pen-fill') }}
                    <span class="visually-hidden">Edit</span>
                  </a>
                {% endif %}

                {% if is_granted(attributes.delete, <?php echo $singular['snake_case']; ?>) %}
                  <a class="btn btn-sm btn-danger btn-action" href="{{ path('<?php echo $singular['snake_case']; ?>_delete', {id: <?php echo $singular['snake_case']; ?>.id}) }}" title="Delete" data-confirm="Are you sure you want to delete this <?php echo $singular['readable']; ?>? Clicking OK will take you to a verification step to delete this entry.">
                    {{ bootstrap_icon('trash-fill') }}
                    <span class="visually-hidden">Delete</span>
                  </a>
                {% endif %}
              </td>
            </tr>
            {% endfor %}
          </tbody>
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
