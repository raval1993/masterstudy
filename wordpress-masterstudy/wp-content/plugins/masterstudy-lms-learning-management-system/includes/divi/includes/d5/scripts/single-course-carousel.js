(function registerDmslmsSingleCourseCarouselModule(windowObj) {
  if (!windowObj) {
    return;
  }

  var metadata = {
    name: 'masterstudy/single-course-carousel',
    d4Shortcode: 'dmslms_single_course_carousel',
    title: 'MS Single Course Carousel',
    titles: 'MS Single Course Carousel',
    moduleClassName: 'dmslms_single_course_carousel',
    moduleOrderClassName: 'dmslms_single_course_carousel',
    category: 'module',
    attributes: {
      module: {
        type: 'object',
        selector: '{{selector}}',
        settings: {
          meta: { adminLabel: {} },
          advanced: { link: {}, text: {}, htmlAttributes: {} },
          decoration: {
            background: {},
            sizing: {},
            spacing: {},
            border: {},
            boxShadow: {},
            filters: {},
            transform: {},
            animation: {},
            overflow: {},
            disabledOn: {},
            transition: {},
            position: {},
            zIndex: {},
            scroll: {},
            sticky: {}
          }
        }
      },
      title: {
        type: 'object',
        selector: '{{selector}}',
        default: { innerContent: { desktop: { value: 'MS Single Course Carousel' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 10,
              render: true,
              attrName: 'title.innerContent',
              label: 'Module Title',
              component: { name: 'divi/text', type: 'field' }
            }
          }
        }
      },
      query: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'none' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 20,
              render: true,
              attrName: 'query.innerContent',
              label: 'Sort By',
              component: {
                name: 'divi/select',
                type: 'field',
                props: { options: { none: { label: 'Default' }, popular: { label: 'Popular' }, free: { label: 'Free' }, rating: { label: 'Rating' } } }
              }
            }
          }
        }
      },
      prevNext: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'enable' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 30,
              render: true,
              attrName: 'prevNext.innerContent',
              label: 'Prev/Next',
              component: { name: 'divi/select', type: 'field', props: { options: { enable: { label: 'Enable' }, disable: { label: 'Disable' } } } }
            }
          }
        }
      },
      taxonomy: {
        type: 'object',
        default: { innerContent: { desktop: { value: '' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 40,
              render: true,
              attrName: 'taxonomy.innerContent',
              label: 'Category IDs',
              component: { name: 'divi/text', type: 'field' }
            }
          }
        }
      }
    },
    customCssFields: {},
    settings: {
      content: 'auto',
      design: 'auto',
      advanced: 'auto',
      groups: {
        contentMain: {
          panel: 'content',
          priority: 10,
          groupName: 'contentMain',
          multiElements: true,
          component: { name: 'divi/composite', props: { groupLabel: 'Content' } }
        }
      }
    }
  };

  var isRegistered = false;

  var getEditRenderer = function getEditRenderer(React, ModuleContainer) {
    return function editRenderer(props) {
      return React.createElement(
        ModuleContainer,
        {
          attrs: props.attrs,
          elements: props.elements,
          id: props.id,
          name: props.name
        },
        props.elements.styleComponents({ attrName: 'module' }),
        React.createElement(
          'div',
          { className: 'dmslms-single-course-carousel-d5-preview' },
          props.elements.render({ attrName: 'title' })
        )
      );
    };
  };

  var registerNow = function registerNow() {
    var moduleLibrary = windowObj.divi && windowObj.divi.moduleLibrary;
    var moduleApi = windowObj.divi && windowObj.divi.module;
    var vendor = windowObj.vendor || {};
    var React = vendor.React;
    var hooks = (windowObj.wp && windowObj.wp.hooks) || (vendor.wp && vendor.wp.hooks);

    if (!moduleLibrary || !moduleLibrary.registerModule || !moduleApi || !moduleApi.ModuleContainer || !React || !hooks) {
      return false;
    }

    try {
      moduleLibrary.registerModule(metadata, {
        renderers: { edit: getEditRenderer(React, moduleApi.ModuleContainer) }
      });
      isRegistered = true;
      return true;
    } catch (e) {
      return false;
    }
  };

  registerNow();

  var attempts = 0;
  var maxAttempts = 80;
  var timer = windowObj.setInterval(function retryLateRegister() {
    attempts += 1;
    if (!isRegistered) {
      registerNow();
    }
    if (isRegistered || attempts >= maxAttempts) {
      windowObj.clearInterval(timer);
    }
  }, 250);
})(window);
