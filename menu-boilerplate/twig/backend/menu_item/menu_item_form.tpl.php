{% extends '@OHMediaBackend/base.html.twig' %}

{% block meta_title %}{{ form_title }}{% endblock %}

{% macro form_row_price(form) %}
  <tr id="{{ form.vars.id }}">
    <td>
      {{ form_widget(form.label, {
        attr: {
          class: 'form-control-sm',
          'aria-label': 'Label',
        },
      }) }}
      {{ form_errors(form.label) }}
    </td>
    <td>
      {{ form_widget(form.amount, {
        attr: {
          'aria-label': 'Amount',
        },
        group_class: 'input-group-sm',
      }) }}
      {{ form_errors(form.amount) }}
    </td>
    <td>
      <div class="row-actions">
        <a class="btn btn-danger" title="Delete Price" href="#" id="{{ form.vars.id }}_delete" aria-label="Delete Price">
          {{ bootstrap_icon('trash') }}
        </a>
      </div>
    </td>
  </tr>
{% endmacro %}

{% block main %}
  <div class="row">
    <div class="col-xl-8">
      <div class="card">
        <div class="card-body">
          <h1 class="card-title">{{ form_title }}</h1>

          {{ form_start(form) }}
            {{ form_row(form.name) }}
            {{ form_row(form.description) }}
            {{ form_row(form.image) }}

            {% do form.prices.setRendered %}

            <fieldset class="form-group">
              {{ form_label(form.prices) }}

              <div id="{{ form.prices.vars.id }}">
                <small class="d-block mb-3">There must be at least 1 price. If there is only 1 price, the label is not shown.</small>

                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Label</th>
                      <th>Amount</th>
                      <th aria-label="Row Actions"></th>
                    </tr>
                  </thead>
                  <tbody id="prices_container">
                    {% for child in form.prices.children %}
                      {{ _self.form_row_price(child) }}
                    {% endfor %}
                  </tbody>
                </table>

                {{ form_errors(form.prices) }}

                <a class="btn btn-secondary btn-sm" id="add_price" href="#">
                  {{ bootstrap_icon('plus-lg') }}
                  Add Price
                </a>
              </div>
            </fieldset>
          {{ form_end(form) }}
        </div>
      </div>
    </div>
  </div>
{% endblock %}

{% block javascripts %}
  <template id="price_prototype">{{ _self.form_row_price(form.prices.vars.prototype) }}</template>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const pricePrototype = document.getElementById('price_prototype');
      const pricePrototypeName = {{ form.prices.vars.prototype.vars.name|js }};

      const addPriceButton = document.getElementById('add_price');
      const pricesContainer = document.getElementById('prices_container');

      let priceIndex = {{ form.prices.children|length - 1 }};

      function onAddOrRemovePrice() {
        const rows = pricesContainer.querySelectorAll('tr');

        if (0 === rows.length) {
          // make sure there is always one entry
          addPrice();
        } else if (1 === rows.length) {
          // if there is only one entry, hide its delete button
          rows[0].querySelector('#' + rows[0].id + '_delete').style.visibility = 'hidden';
        } else {
          // make sure all delete buttons are visible
          rows.forEach(row => {
            row.querySelector('#' + row.id + '_delete').style.visibility = '';
          });
        }
      }

      async function initRow(row) {
        const deleteButton = document.getElementById(row.id + '_delete');

        deleteButton.addEventListener('click', async function(e) {
          e.preventDefault();

          const confirmed = await customConfirm('Are you sure you want to delete this entry?');

          if (confirmed) {
            row.remove();

            onAddOrRemovePrice();
          }
        });
      }

      function addPrice() {
        priceIndex++;

        const row = pricePrototype.content.firstElementChild.cloneNode(true);

        row.innerHTML = row.innerHTML.replaceAll(pricePrototypeName, priceIndex);

        row.id = row.id.replace(pricePrototypeName, priceIndex);

        pricesContainer.append(row);

        initRow(row);

        onAddOrRemovePrice();
      }

      addPriceButton.addEventListener('click', async function(e) {
        e.preventDefault();

        addPrice();
      });

      const rows = pricesContainer.querySelectorAll('tr');

      rows.forEach(initRow);

      if (!rows.length) {
        addPrice();
      }

      onAddOrRemovePrice();
    });
  </script>
{% endblock %}
