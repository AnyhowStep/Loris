!function(e){function t(n){if(r[n])return r[n].exports;var o=r[n]={exports:{},id:n,loaded:!1};return e[n].call(o.exports,o,o.exports,t),o.loaded=!0,o.exports}var r={};return t.m=e,t.c=r,t.p="",t(0)}([function(e,t){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function n(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}function o(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}var a=function(){function e(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(t,r,n){return r&&e(t.prototype,r),n&&e(t,n),t}}(),l=function(e){function t(e){r(this,t);var o=n(this,(t.__proto__||Object.getPrototypeOf(t)).call(this,e));return o.state={data:null},o}return o(t,e),a(t,[{key:"deleteCallback",value:function(e){var t=this.state.data;if(t&&t.arr){console.log(t.arr),console.log("in delete",t.arr.length);for(i in t.arr)if(t.arr[i].id==e){t.arr.splice(i,1),console.log(t.arr),console.log("in delete",t.arr.length),this.setState({data:{arr:[t.arr[0]]}});break}}}},{key:"render",value:function(){var e=this.state.data,t=[];if(e&&e.arr){console.log("in render",e.arr.length);for(i in e.arr)t.push(React.createElement(Row,{data:e.arr[i],deleteCallback:this.deleteCallback.bind(this)}))}return React.createElement("tbody",null,t)}},{key:"componentDidMount",value:function(){$.ajax({type:"GET",url:"/acknowledgements/ajax/fetch_all_of_center.php",data:{center_id:this.props.center_id},dataType:"json",success:function(e){console.log(e),this.setState({data:e})}.bind(this)})}}]),t}(React.Component);window.TBody=l}]);
//# sourceMappingURL=TBody.js.map