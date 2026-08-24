(function registerDmslmsCoursesSearchboxModule(windowObj) {
  if (!windowObj) {
    return;
  }
  var metadata = {
    name: 'masterstudy/courses-searchbox',
    d4Shortcode: 'dmslms_courses_searchbox',
    title: 'MS Courses Searchbox',
    titles: 'MS Courses Searchbox',
    moduleClassName: 'dmslms_courses_searchbox',
    moduleOrderClassName: 'dmslms_courses_searchbox',
    category: 'module',
    attributes: {
      module: {
        type: 'object',
        selector: '{{selector}}',
        default: {
          meta: {
            adminLabel: {
              desktop: {
                value: 'MS Courses Searchbox'
              }
            }
          }
        },
        settings: {
          meta: {
            adminLabel: {}
          },
          advanced: {
            link: {},
            text: {},
            htmlAttributes: {}
          },
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
        default: { innerContent: { desktop: { value: 'MS Courses Search' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 10,
              render: true,
              attrName: 'title.innerContent',
              label: 'Module Title',
              description: 'Text entered here will appear as title.',
              features: {
                sticky: false,
                dynamicContent: {
                  type: 'text'
                }
              },
              component: {
                name: 'divi/text',
                type: 'field'
              }
            }
          }
        }
      },
      searchStyle: {
        type: 'object',
        default: {
          innerContent: {
            desktop: {
              value: 'style_1'
            }
          }
        },
        settings: {
          innerContent: {
            groupType: 'group-items',
            items: {
              src: {
                groupSlug: 'contentMain',
                priority: 20,
                render: true,
                subName: 'value',
                label: 'Style',
                description: 'Use style_1 or style_2.',
                features: {
                  sticky: false,
                  responsive: false,
                  hover: false,
                  dynamicContent: false
                },
                component: {
                  name: 'divi/select',
                  type: 'field',
                  props: {
                    options: {
                      style_1: { label: 'Style 1' },
                      style_2: { label: 'Style 2' }
                    }
                  }
                }
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
          component: {
            name: 'divi/composite',
            props: {
              groupLabel: 'Content'
            }
          }
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
          { className: 'dmslms-courses-searchbox-d5-preview' },
          props.elements.render({ attrName: 'title' })
        )
      );
    };
  };

  var registerNow = function registerNow(source) {
    var moduleLibrary = windowObj.divi && windowObj.divi.moduleLibrary;
    var moduleApi = windowObj.divi && windowObj.divi.module;
    var vendor = windowObj.vendor || {};
    var React = vendor.React;
    var hooks = (windowObj.wp && windowObj.wp.hooks) || (vendor.wp && vendor.wp.hooks);

    if (
      !moduleLibrary ||
      !moduleLibrary.registerModule ||
      !moduleApi ||
      !moduleApi.ModuleContainer ||
      !React ||
      !hooks
    ) {
      return false;
    }

    try {
      moduleLibrary.registerModule(metadata, {
        renderers: {
          edit: getEditRenderer(React, moduleApi.ModuleContainer)
        }
      });
      isRegistered = true;

      return true;
    } catch (e) {
      return false;
    }
  };

  // Try immediate registration first.
  registerNow('immediate');

  // Keep trying while Divi app finishes loading.
  var attempts = 0;
  var maxAttempts = 80;
  var timer = windowObj.setInterval(function retryLateRegister() {
    attempts += 1;

    if (!isRegistered) {
      registerNow('polling');
    }

    if (isRegistered || attempts >= maxAttempts) {
      windowObj.clearInterval(timer);
    }
  }, 250);
})(window);
