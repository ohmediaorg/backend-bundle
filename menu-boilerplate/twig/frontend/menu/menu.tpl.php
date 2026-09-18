{% macro <?php echo $singular['snake_case']; ?>_item_tag(svg, text) %}
  <li class="<?php echo $singular['kebab_case']; ?>-item__tag" data-bs-toggle="tooltip" data-bs-title="{{ text|e('html_attr') }}" data-bs-placement="bottom">
    <span class="<?php echo $singular['kebab_case']; ?>-item__tag-icon">
      {% include '@frontend/menu/svg/' ~ svg %}
    </span>
    <span class="<?php echo $singular['kebab_case']; ?>-item__tag-copy">{{ text }}</span>
  </li>
{% endmacro %}

{% macro <?php echo $singular['snake_case']; ?>_item_tags(item) %}
  {% if item.favourite %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'favourite.svg.twig',
      'Fan Favourite'
    ) }}
  {% endif %}

  {% if item.dairyFree %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'dairy_free.svg.twig',
      'Dairy-Free'
    ) }}
  {% endif %}

  {% if item.eggs %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'eggs.svg.twig',
      'Contains Eggs'
    ) }}
  {% endif %}

  {% if item.glutenFree %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'gluten_free.svg.twig',
      'Gluten Free'
    ) }}
  {% endif %}

  {% if item.organic %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'organic.svg.twig',
      'Organic'
    ) }}
  {% endif %}

  {% if item.spicy %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'spicy.svg.twig',
      'Spicy'
    ) }}
  {% endif %}

  {% if item.vegan %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'vegan.svg.twig',
      'Vegan'
    ) }}
  {% endif %}

  {% if item.vegetarian %}
    {{ _self.<?php echo $singular['snake_case']; ?>_item_tag(
      'vegetarian.svg.twig',
      'Vegetarian'
    ) }}
  {% endif %}
{% endmacro %}

<nav class="<?php echo $singular['kebab_case']; ?>-nav">
  <div class="<?php echo $singular['kebab_case']; ?>-nav__inner">
    <div class="<?php echo $singular['kebab_case']; ?>-nav__menus">
      <ul>
        {% for menu in menus %}
          <li>
            <a href="#" {% if loop.first %}class="active"{% endif %} data-<?php echo $singular['kebab_case']; ?>-picker="{{ menu.entity.id }}">
              {{ menu.entity }}
            </a>
          </li>
        {% endfor %}
      </ul>
    </div>
  </div>
</nav>

{% for menu in menus %}
  <div class="menu" data-menu="{{ menu.entity.id }}" {% if not loop.first %}style="display:none"{% endif %}>
    <div class="<?php echo $singular['kebab_case']; ?>-nav--sections__wrapper">
      <nav class="<?php echo $singular['kebab_case']; ?>-nav--sections">
        <ul>
          {% for section in menu.sections %}
            <li class="nav-links__item">
              <a class="nav-links__link" href="#{{ section.entity.slug }}-{{ section.entity.id }}">
                {{ section.entity }}
              </a>
            </li>
          {% endfor %}
        </ul>
      </nav>
    </div>

    <div class="<?php echo $singular['snake_case']; ?>__inner">
      {% for section in menu.sections %}
        <div id="{{ section.entity.slug }}-{{ section.entity.id }}" class="<?php echo $singular['kebab_case']; ?>-section">
          <div class="<?php echo $singular['kebab_case']; ?>-section__inner">
            <h2 class="<?php echo $singular['kebab_case']; ?>-section__title">{{ section.entity }}</h2>
            {% if section.entity.description %}
              <div class="<?php echo $singular['kebab_case']; ?>-section__description">
                {{ wysiwyg(section.entity.description, null, false) }}
              </div>
            {% endif %}

            <div class="<?php echo $singular['kebab_case']; ?>-items">
              {% for item in section.items %}
                <div class="<?php echo $singular['kebab_case']; ?>-item">
                  <div class="<?php echo $singular['kebab_case']; ?>-item__copy">
                    <h3 class="<?php echo $singular['kebab_case']; ?>-item__title">{{ item }}</h3>
                    <div class="<?php echo $singular['kebab_case']; ?>-item__description">
                      {{ wysiwyg(item.description, null, false) }}
                    </div>
                    {% if item.hasTags %}
                      <div class="<?php echo $singular['kebab_case']; ?>-item__tags">
                        <ul>{{ _self.<?php echo $singular['snake_case']; ?>_item_tags(item) }}</ul>
                      </div>
                    {% endif %}
                    <div class="<?php echo $singular['kebab_case']; ?>-item__prices">
                      {% if item.prices.count == 1 %}
                        <div class="<?php echo $singular['kebab_case']; ?>-item__price__amount">
                          ${{ item.prices[0].amount }}
                        </div>
                      {% else %}
                        {% for price in item.prices %}
                          <div class="<?php echo $singular['kebab_case']; ?>-item__price__label">
                            {{ price.label }}
                          </div>
                          <div class="<?php echo $singular['kebab_case']; ?>-item__price__amount">
                            ${{ price.amount }}
                          </div>
                        {% endfor %}
                      {% endif %}
                    </div>
                  </div>

                  {% if item.image %}
                    <div class="<?php echo $singular['kebab_case']; ?>-item__image">
                      {{ image_tag(item.image, {
                        width: 350,
                        height: 219,
                      }) }}
                    </div>
                  {% endif %}
                </div>
              {% endfor %}
            </div>
          </div>
        </div>
      {% endfor %}
    </div>
  </div>
{% endfor %}

<script>
document.addEventListener('DOMContentLoaded', function() {
  const pickers = document.querySelectorAll('[data-<?php echo $singular['kebab_case']; ?>-picker]');

  const allMenus = document.querySelectorAll('[data-menu]');

  pickers.forEach(function(picker) {
    const id = picker.dataset.menuPicker;

    const menu = document.querySelector('[data-menu="' + id + '"]');

    const menuNavSections = menu.querySelector('.<?php echo $singular['kebab_case']; ?>-nav--sections');

    window.StickyJS(menuNavSections, {
      classPrefix: 'sticky',
      parentNode: menu,
    });

    const scrollSpy = new window.Bootstrap.ScrollSpy(menu, {
      target: menuNavSections,
    });

    menu.addEventListener('activate.bs.scrollspy', function(e) {
      const menuNavSectionsRect = menuNavSections.getBoundingClientRect();

      const scrollableWidth = menuNavSections.scrollWidth - menuNavSectionsRect.width;

      if (scrollableWidth > 1) {
        const link = e.relatedTarget;

        const linkRect = link.getBoundingClientRect();

        const linkLeft = linkRect.left + menuNavSections.scrollLeft;

        let left = linkLeft - (menuNavSectionsRect.width - linkRect.width) / 2;

        if (left < 0) {
          left = 0;
        }

        menuNavSections.scroll({
          top: 0,
          left: left,
          behavior: 'smooth',
        });
      }
    });

    picker.addEventListener('click', (e) => {
      e.preventDefault();

      allMenus.forEach(function(menu) {
        menu.style.display = 'none';
      });

      menu.style.display = '';
      scrollSpy.refresh();

      pickers.forEach(function(p) {
        p.classList.remove('active');
      });

      picker.classList.add('active');
    });
  });
});
</script>

<script type="application/ld+json">{{ schema|js }}</script>
