(function registerDmslmsBlogListModule(windowObj) {
  if (!windowObj) {
    return;
  }

  var metadata = {
    name: 'masterstudy/blog-list',
    d4Shortcode: 'dmslms_blog_list',
    title: 'MS Blog Module',
    titles: 'MS Blog Module',
    moduleClassName: 'dmslms_blog_list',
    moduleOrderClassName: 'dmslms_blog_list',
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
        default: { innerContent: { desktop: { value: 'MS Latest Articles' } } },
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
      count: {
        type: 'object',
        default: { innerContent: { desktop: { value: '3' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 20,
              render: true,
              attrName: 'count.innerContent',
              label: 'Post Count',
              component: {
                name: 'divi/select',
                type: 'field',
                props: { options: { '1': { label: '1' }, '2': { label: '2' }, '3': { label: '3' }, '4': { label: '4' }, '6': { label: '6' } } }
              }
            }
          }
        }
      },
      type: {
        type: 'object',
        default: { innerContent: { desktop: { value: 'list' } } },
        settings: {
          innerContent: {
            groupType: 'group-item',
            item: {
              groupSlug: 'contentMain',
              priority: 30,
              render: true,
              attrName: 'type.innerContent',
              label: 'View Type',
              component: {
                name: 'divi/select',
                type: 'field',
                props: { options: { list: { label: 'List' }, grid: { label: 'Grid' } } }
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
          { className: 'dmslms-blog-list-d5-preview' },
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
