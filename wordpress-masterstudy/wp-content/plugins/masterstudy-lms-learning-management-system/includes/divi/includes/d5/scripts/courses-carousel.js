(function registerDmslmsCoursesCarouselModule(windowObj) {
  if (!windowObj) {
    return;
  }

  var metadata = {
    name: 'masterstudy/courses-carousel',
    d4Shortcode: 'dmslms_courses_carousel',
    title: 'MS Courses Carousel',
    titles: 'MS Courses Carousel',
    moduleClassName: 'dmslms_courses_carousel',
    moduleOrderClassName: 'dmslms_courses_carousel',
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
        default: { innerContent: { desktop: { value: 'MS Featured Courses' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentCarousel',
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
      query: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'none' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentCarousel',
              priority: 20,
              render: true,
              attrName: 'query.innerContent',
              label: 'Sort By',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
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
              groupSlug: 'contentCarousel',
              priority: 30,
              render: true,
              attrName: 'prevNext.innerContent',
              label: 'Previous/Next Buttons',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: { name: 'divi/select', type: 'field', props: { options: { enable: { label: 'Enable' }, disable: { label: 'Disable' } } } }
            }
          }
        }
      },
      showCategories: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'disable' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentCarousel',
              priority: 40,
              render: true,
              attrName: 'showCategories.innerContent',
              label: 'Show Categories',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: { name: 'divi/select', type: 'field', props: { options: { enable: { label: 'Enable' }, disable: { label: 'Disable' } } } }
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
              groupSlug: 'contentCarousel',
              priority: 50,
              render: true,
              attrName: 'perRow.innerContent',
              label: 'Courses Per Row',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: { name: 'divi/select', type: 'field', props: { options: { '1': { label: '1' }, '2': { label: '2' }, '3': { label: '3' }, '4': { label: '4' }, '5': { label: '5' }, '6': { label: '6' } } } }
            }
          }
        }
      },
      postsPerPage: {
        type: 'object',
        default: { innerContent: { desktop: { value: '' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentCarousel',
              priority: 60,
              render: true,
              attrName: 'postsPerPage.innerContent',
              label: 'Courses Per Page',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: { name: 'divi/select', type: 'field', props: { options: { '': { label: 'Default' }, '6': { label: '6' }, '8': { label: '8' }, '12': { label: '12' }, '16': { label: '16' }, '24': { label: '24' } } } }
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
              groupSlug: 'contentCarousel',
              priority: 70,
              render: true,
              attrName: 'imageSize.innerContent',
              label: 'Image Size',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
              component: { name: 'divi/select', type: 'field', props: { options: { '': { label: 'Default' }, thumbnail: { label: 'Thumbnail' }, medium: { label: 'Medium' }, large: { label: 'Large' }, full: { label: 'Full' } } } }
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
              groupSlug: 'contentCarousel',
              priority: 80,
              render: true,
              attrName: 'taxonomy.innerContent',
              label: 'Category IDs',
              description: 'Comma separated IDs, e.g. 2,5,9',
              features: { sticky: false, responsive: false, hover: false, dynamicContent: false },
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
        contentCarousel: {
          panel: 'content',
          priority: 10,
          groupName: 'contentCarousel',
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
          { className: 'dmslms-courses-carousel-d5-preview' },
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
