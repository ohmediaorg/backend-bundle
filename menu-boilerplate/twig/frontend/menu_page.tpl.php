{% extends '@frontend/extend/page_base.html.twig' %}

{% block main %}
  <section class="title-banner">
    {% if content_image_exists('banner') %}
      <div class="title-banner__image">
        {{ content_image_tag('banner') }}

        <div class="title-banner__image-shade"></div>
      </div>
    {% else %}
      <div class="title-banner__image">
        <img src="{{ asset('frontend/images/placeholders/placeholder__hero--fullscreen.png', 'frontend') }}" alt="">

        <div class="title-banner__image-shade"></div>
      </div>
    {% endif %}
    <div class="title-banner__inner">
      <div class="title-banner__copy">
        <h1 class="title-banner__title">{{ content_text('title') }}</h1>
      </div>
    </div>
  </section>

  {{ <?php echo $singular['snake_case']; ?>() }}
{% endblock %}
