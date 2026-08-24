(function registerAllDmslmsModules(windowObj) {
  if (!windowObj) {
    return;
  }

  var metadataList = Array.isArray(windowObj.dmslmsD5ModuleMetadataList) ? windowObj.dmslmsD5ModuleMetadataList : [];
  if (!metadataList.length) {
    return;
  }

  var registered = {};

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
          { className: 'dmslms-d5-preview' },
          props.elements.render({ attrName: 'title' })
        )
      );
    };
  };

  var tryRegisterAll = function tryRegisterAll() {
    var moduleLibrary = windowObj.divi && windowObj.divi.moduleLibrary;
    var moduleApi = windowObj.divi && windowObj.divi.module;
    var vendor = windowObj.vendor || {};
    var React = vendor.React;
    var hooks = (windowObj.wp && windowObj.wp.hooks) || (vendor.wp && vendor.wp.hooks);

    if (!moduleLibrary || !moduleLibrary.registerModule || !moduleApi || !moduleApi.ModuleContainer || !React || !hooks) {
      return false;
    }

    metadataList.forEach(function eachMetadata(metadata) {
      if (!metadata || !metadata.name || registered[metadata.name]) {
        return;
      }
      try {
        moduleLibrary.registerModule(metadata, {
          renderers: { edit: getEditRenderer(React, moduleApi.ModuleContainer) }
        });
        registered[metadata.name] = true;
      } catch (e) {}
    });

    return true;
  };

  tryRegisterAll();

  var attempts = 0;
  var maxAttempts = 80;
  var timer = windowObj.setInterval(function retryRegister() {
    attempts += 1;
    tryRegisterAll();
    if (attempts >= maxAttempts) {
      windowObj.clearInterval(timer);
    }
  }, 250);
})(window);
