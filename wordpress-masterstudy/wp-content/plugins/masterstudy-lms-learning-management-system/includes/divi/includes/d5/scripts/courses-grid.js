(function registerDmslmsCoursesGridModule(windowObj) {
  if (!windowObj) {
    return;
  }

  var metadata = {
    name: 'masterstudy/courses-grid',
    d4Shortcode: 'dmslms_courses_grid',
    title: 'MS Courses Grid',
    titles: 'MS Courses Grid',
    moduleClassName: 'dmslms_courses_grid',
    moduleOrderClassName: 'dmslms_courses_grid',
    category: 'module',
    attributes: {
      module: {
        type: 'object',
        selector: '{{selector}}',
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
        default: { innerContent: { desktop: { value: 'MS Courses' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 10,
              render: true,
              attrName: 'title.innerContent',
              label: 'Module Title',
              description: 'Input your desired heading here.',
              features: { sticky: false, dynamicContent: { type: 'text' } },
              component: { name: 'divi/text', type: 'field' }
            }
          }
        }
      },
      hideTopBar: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'showing' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 20,
              render: true,
              attrName: 'hideTopBar.innerContent',
              label: 'Hide Top Bar',
              description: 'Use show or hide.',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: {
                name: 'divi/select',
                type: 'field',
                  props: { options: { showing: { label: 'Show' }, hidden: { label: 'Hide' } } }
              }
            }
          }
        }
      },
      loadMore: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'showing' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 30,
              render: true,
              attrName: 'loadMore.innerContent',
              label: 'Load More',
              description: 'Use show or hide.',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: {
                name: 'divi/select',
                type: 'field',
                  props: { options: { showing: { label: 'Show' }, hidden: { label: 'Hide' } } }
              }
            }
          }
        }
      },
      sortCourses: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'showing' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 40,
              render: true,
              attrName: 'sortCourses.innerContent',
              label: 'Sort Courses',
              description: 'Use show or hide.',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: {
                name: 'divi/select',
                type: 'field',
                  props: { options: { showing: { label: 'Show' }, hidden: { label: 'Hide' } } }
              }
            }
          }
        }
      },
      imageSize: {
        type: 'object',
        default: { innerContent: { desktop: { value: '' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 50,
              render: true,
              attrName: 'imageSize.innerContent',
              label: 'Image Size',
              description: 'Example: thumbnail, medium, large, full.',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: {
                name: 'divi/select',
                type: 'field',
                props: {
                  options: {
                      '': { label: 'Default' },
                    thumbnail: { label: 'Thumbnail' },
                    medium: { label: 'Medium' },
                    large: { label: 'Large' },
                    full: { label: 'Full' }
                  }
                }
              }
            }
          }
        }
      },
      perRow: {
        type: 'object',
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 60,
              render: true,
              attrName: 'perRow.innerContent',
              label: 'Courses Per Row',
              description: 'Number of courses per row.',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: {
                name: 'divi/select',
                type: 'field',
                props: {
                  options: {
                    '1': { label: '1' },
                    '2': { label: '2' },
                    '3': { label: '3' },
                    '4': { label: '4' },
                    '5': { label: '5' },
                    '6': { label: '6' }
                  }
                }
              }
            }
          }
        }
      },
      perPage: {
        type: 'object',
        default: { innerContent: { desktop: { value: '' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentGrid',
              priority: 70,
              render: true,
              attrName: 'perPage.innerContent',
              label: 'Courses Per Page',
              description: 'Number of courses per page.',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: {
                name: 'divi/select',
                type: 'field',
                props: {
                  options: {
                      '': { label: 'Default' },
                    '6': { label: '6' },
                    '8': { label: '8' },
                    '10': { label: '10' },
                    '12': { label: '12' },
                    '16': { label: '16' },
                    '24': { label: '24' }
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
        contentGrid: {
          panel: 'content',
          priority: 10,
          groupName: 'contentGrid',
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
          { className: 'dmslms-courses-grid-d5-preview' },
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

  registerNow('immediate');

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
