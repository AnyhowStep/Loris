!function(modules){function __webpack_require__(moduleId){if(installedModules[moduleId])return installedModules[moduleId].exports;var module=installedModules[moduleId]={exports:{},id:moduleId,loaded:!1};return modules[moduleId].call(module.exports,module,module.exports,__webpack_require__),module.loaded=!0,module.exports}var installedModules={};return __webpack_require__.m=modules,__webpack_require__.c=installedModules,__webpack_require__.p="",__webpack_require__(0)}({0:function(module,exports,__webpack_require__){"use strict";function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj}}var _Instrument=__webpack_require__(14),_Instrument2=_interopRequireDefault(_Instrument);window.setData=function(container_id,data){ReactDOM.render(React.createElement(_Instrument2.default,{data:data}),document.getElementById(container_id))}},14:function(module,exports){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}function _possibleConstructorReturn(self,call){if(!self)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!call||"object"!=typeof call&&"function"!=typeof call?self:call}function _inherits(subClass,superClass){if("function"!=typeof superClass&&null!==superClass)throw new TypeError("Super expression must either be null or a function, not "+typeof superClass);subClass.prototype=Object.create(superClass&&superClass.prototype,{constructor:{value:subClass,enumerable:!1,writable:!0,configurable:!0}}),superClass&&(Object.setPrototypeOf?Object.setPrototypeOf(subClass,superClass):subClass.__proto__=superClass)}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),Instrument=function(_React$Component){function Instrument(props){return _classCallCheck(this,Instrument),_possibleConstructorReturn(this,(Instrument.__proto__||Object.getPrototypeOf(Instrument)).call(this,props))}return _inherits(Instrument,_React$Component),_createClass(Instrument,[{key:"getInputMetadata",value:function(i){return this.props.data[i]}},{key:"getInputMetadataCount",value:function(i){return this.props.data.length}},{key:"renderInput",value:function(inputMetadata){}},{key:"render",value:function(){for(var inputArr=[],i=0;i<this.getInputMetadataCount();++i){var cur=this.getInputMetadata(i);inputArr.push(this.renderInput(cur))}return React.createElement("div",null,inputArr)}}]),Instrument}(React.Component);exports.default=Instrument}});
//# sourceMappingURL=main.js.map