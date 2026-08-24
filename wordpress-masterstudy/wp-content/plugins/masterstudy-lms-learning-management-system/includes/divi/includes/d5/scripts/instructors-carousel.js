(function registerDmslmsInstructorsCarouselModule(windowObj) {
  if (!windowObj) {
    return;
  }

  var metadata = {
    name: 'masterstudy/instructors-carousel',
    d4Shortcode: 'dmslms_instructors_carousel',
    title: 'MS Instructors Carousel',
    titles: 'MS Instructors Carousel',
    moduleClassName: 'dmslms_instructors_carousel',
    moduleOrderClassName: 'dmslms_instructors_carousel',
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
        default: { innerContent: { desktop: { value: 'MS Our Instructors' } } },
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
      perRow: {
        type: 'object',
        default: { innerContent: { desktop: { value: '4' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 20,
              render: true,
              attrName: 'perRow.innerContent',
              label: 'Per Row',
              component: {
                name: 'divi/select',
                type: 'field',
                props: { options: { '1': { label: '1' }, '2': { label: '2' }, '3': { label: '3' }, '4': { label: '4' }, '6': { label: '6' } } }
              }
            }
          }
        }
      },
      style: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'style_1' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 30,
              render: true,
              attrName: 'style.innerContent',
              label: 'Style',
              component: {
                name: 'divi/select',
                type: 'field',
                props: { options: { style_1: { label: 'Style 1' }, style_2: { label: 'Style 2' } } }
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
              priority: 40,
              render: true,
              attrName: 'prevNext.innerContent',
              label: 'Prev/Next',
              component: { name: 'divi/select', type: 'field', props: { options: { enable: { label: 'Enable' }, disable: { label: 'Disable' } } } }
            }
          }
        }
      },
      sort: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'default' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 50,
              render: true,
              attrName: 'sort.innerContent',
              label: 'Sort',
              component: {
                name: 'divi/select',
                type: 'field',
                props: { options: { default: { label: 'Default' }, rating: { label: 'Rating' } } }
              }
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
          { className: 'dmslms-instructors-carousel-d5-preview' },
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
