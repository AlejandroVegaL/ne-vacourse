!function(){var moduleFactory=function($){var module=this;$.require().script("mvc/event.default").done(function(){var exports=function(){var current,rnamespaces=/\.(.*)$/,returnFalse=function(){return false},returnTrue=function(){return true};$.Event.prototype.isPaused=returnFalse;$.Event.prototype.pause=function(){this.pausedState={isDefaultPrevented:this.isDefaultPrevented()?returnTrue:returnFalse,isPropagationStopped:this.isPropagationStopped()?returnTrue:returnFalse};this.stopImmediatePropagation();this.preventDefault();this.isPaused=returnTrue};$.Event.prototype.resume=function(){var handleObj=this.handleObj,currentTarget=this.currentTarget;var origType=$.event.special[handleObj.origType],origHandle=origType&&origType.handle;if(!origType){$.event.special[handleObj.origType]={}}$.event.special[handleObj.origType].handle=function(ev){if(ev.handleObj===handleObj&&ev.currentTarget===currentTarget){if(!origType){delete $.event.special[handleObj.origType]}else{$.event.special[handleObj.origType].handle=origHandle}}};delete this.pausedState;this.isPaused=this.isImmediatePropagationStopped=returnFalse;if(!this.isPropagationStopped()){$.event.trigger(this,[],this.target)}}};exports();module.resolveWith(exports)})};dispatch("mvc/event.pause").containing(moduleFactory).to("Foundry/2.1 Modules")}();