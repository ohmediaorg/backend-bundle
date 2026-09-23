.<?php echo $singular['kebab_case']; ?>-nav {
  padding: 2rem 0;
  border-bottom: 1px solid #cbbca7;

  .<?php echo $singular['kebab_case']; ?>-nav__inner {
    @include pulsar-container($oh-container-width-large);
  }

  .<?php echo $singular['kebab_case']; ?>-nav__menus {
    ul {
      @extend .list-unstyled;

      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem 2rem;
      margin: 0;

      li {
        a {
          @extend .btn;
          @extend .btn-outline-primary;
          min-width: 150px;
        }
      }
    }
  }
}

.<?php echo $singular['kebab_case']; ?>-nav--sections {
  ul {
    @extend .nav-links;
    @extend .nav-links--vertical;

    min-width: 0 !important;
  }

  @include media-breakpoint-down(md) {
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    @include overflow-shadow-x($white, $gray-600, 0.5);

    transition: box-shadow 0.2s ease;

    &.is-sticky {
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    ul {
      width: max-content;
      margin: 0 auto;
      background: transparent !important;

      li {
        display: inline-block;

        a {
          display: inline-block;
          white-space: nowrap;
        }
      }
    }
  }
}

.<?php echo $singular['kebab_case']; ?> {
  position: relative;

  .dropdown-menu {
    padding: 0;
  }

  .<?php echo $singular['kebab_case']; ?>-section {
    padding: 2rem 0 1rem;

    &:last-of-type {
      border-bottom: none;
    }

    .<?php echo $singular['kebab_case']; ?>-section__description {
      padding-bottom: 2rem;

      > *:last-child {
        margin-bottom: 0;
      }
    }

    .<?php echo $singular['kebab_case']; ?>-section__title {
      @extend %oh-h3;
      margin-bottom: 1.25rem;
    }

    .<?php echo $singular['kebab_case']; ?>-section__subtitle {
      @include font-size(18px);
      font-weight: 700;
    }
  }

  @include media-breakpoint-up(md) {
    display: grid;
    grid-template-columns: 1fr 3fr;
    gap: 1.5rem;

    @include pulsar-container($oh-container-width-large);

    .<?php echo $singular['kebab_case']; ?>__inner {
      margin: 0;
    }

    .<?php echo $singular['kebab_case']; ?>-nav--sections {
      padding: 2rem 0;
    }
  }

  @include media-breakpoint-down(md) {
    .<?php echo $singular['kebab_case']; ?>__inner {
      @include pulsar-container($oh-container-width-medium);
    }

    .dropdown-menu {
      padding: 0.5rem;
    }
  }
}

.<?php echo $singular['kebab_case']; ?>-item {
  padding: 2rem 0;
  border-bottom: 1px solid #cbbca7;
  gap: 1rem 2rem;

  &:first-of-type {
    padding-top: 0;
  }

  &:last-of-type {
    border-bottom: none;
  }

  .<?php echo $singular['kebab_case']; ?>-item__title {
    @extend %oh-h5;
    @include font-size(20px);
    margin-bottom: 0.5rem;
  }

  .<?php echo $singular['kebab_case']; ?>-item__copy {
    flex: 1 1 75%;
  }

  .<?php echo $singular['kebab_case']; ?>-item__image {
    width: 100%;
    flex: 1 1 50%;

    img {
      border-radius: 6px;
    }
  }

  .<?php echo $singular['kebab_case']; ?>-item__description {
    > *:last-child {
      margin-bottom: 0;
    }
  }

  .<?php echo $singular['kebab_case']; ?>-item__tags {
    margin-top: 1rem;

    ul {
      @extend .list-unstyled;
      margin: 0;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .<?php echo $singular['kebab_case']; ?>-item__tag-icon {
      margin-right: 0.25rem;
    }

    .<?php echo $singular['kebab_case']; ?>-item__tag-copy {
      @extend .visually-hidden;
    }
  }

  .<?php echo $singular['kebab_case']; ?>-item__prices {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.75rem 1.5rem;
    margin-top: 1rem;
    @include font-size(18px);
    line-height: 1.333em;

    .<?php echo $singular['kebab_case']; ?>-item__price__label {
      min-width: 30px;
      font-weight: 600;
    }

    .<?php echo $singular['kebab_case']; ?>-item__price__amount {
      font-weight: 500;
    }
  }

  @include media-breakpoint-up(sm) {
    display: flex;

    .<?php echo $singular['kebab_case']; ?>-item__image {
      max-width: 250px;
    }
  }

  @include media-breakpoint-down(sm) {
    display: grid;

    .<?php echo $singular['kebab_case']; ?>-item__image {
      order: -1;
      max-width: 350px;
    }
  }

  @include media-breakpoint-up(md) {
    .<?php echo $singular['kebab_case']; ?>-item__prices {
      gap: 0.75rem 2rem;
    }
  }

  @include media-breakpoint-up(lg) {
    .<?php echo $singular['kebab_case']; ?>-item__prices {
      gap: 0.75rem 2.75rem;
    }
  }
}
