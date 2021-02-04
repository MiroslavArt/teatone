BX.namespace('iTrack.Crm.FillSignal');

BX.iTrack.Crm.FillSignal = {
    kanban: null,
    init: function(type) {
        console.log(type)
        switch(type) {
            case 'detail':
                BX.addCustomEvent('BX.Crm.EntityEditor:onInit', BX.delegate(this.detailHandler, this));
                BX.addCustomEvent('BX.Crm.EntityEditorSection:onLayout', BX.delegate(this.detailHandler, this));
                break;
            case 'kanban':
                //console.log(this)
                BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
                //BX.addCustomEvent('Kanban.Grid:ready', BX.delegate(this.kanbanHandler, this));
                break;
            case 'list':
                //console.log(this)
                BX.addCustomEvent('Grid::ready', BX.delegate(this.listHandler, this));
                //BX.addCustomEvent('Grid::updated', BX.delegate(this.listHandler, this));
                break;
        }
    },
    detailHandler: function(editor, data) {
        console.log(editor)
        console.log(data)
        //document.querySelectorAll('.crm-entity-widget-client-contact-phone').forEach(function (el) {
        //    this.processPhone(el);
        //}.bind(this));
        //document.querySelectorAll('.crm-entity-phone-number').forEach(function (el) {
        //    this.processPhone(el);
        //}.bind(this));
    },
    kanbanHandler: function(grid){
        this.kanban = grid;
        console.log(this.kanban)
        var collectSignals = []
        for(var i in grid.items) {
            if(i>0) {
            //if(grid.items[i].data.hasOwnProperty('phone')) {
            //    if(grid.items[i].data.phone.hasOwnProperty('contact')) {
            //        if (grid.items[i].data.phone.contact.length) {
            //            var localValue = localStorage.getItem(grid.items[i].data.phone.contact[0].value);
            //            if (localValue == null) {
                            collectSignals.push(i);
            //            }
            //        }
            //    }
            //}
            }
        }
        console.log(collectSignals)
        if(collectSignals.length) {
            this.requestSignals(collectSignals).then(function(response) {
                console.log(response);
                //this.processCollectionResponse(response);
                //this.processKanbanSignals();
            }.bind(this), function(error){
                console.log(error);
            }.bind(this));
        } else {
            //this.processKanbanSignals();
        }
    },
    listHandler: function(grid){
        this.list = grid;
        console.log(this.list)
    },
    requestSignals: function(signal) {
        return BX.ajax.runAction('itrack:custom.api.signal.getSignal', {
            data: {
                signals: signal
            }
        });
    },
    processCollectionResponse: function(response) {
        console.log(response);
        if(response.hasOwnProperty('status')) {
            if(response.status == 'success') {
                if(response.data.length) {
                    for(var i in response.data) {
                        localStorage.setItem(response.data[i].phone, response.data[i].timezone);
                    }
                }
            }
        }
    },
    processKanbanSignals: function() {
        var items = this.kanban.items;
        for(var i in items) {
            if(items[i].data.hasOwnProperty('phone')) {
                if(items[i].data.phone.hasOwnProperty('contact')) {
                    if (items[i].data.phone.contact.length) {
                        var localValue = localStorage.getItem(items[i].data.phone.contact[0].value);
                        if (localValue !== null) {
                            if (!items[i].contactBlock.querySelector('.itrack-custom-crm-phonetime__phone-block')) {
                                var timeNode = this.createTimeNodeForContactBlock();
                                BX.append(timeNode, items[i].contactBlock);
                                new BX.iTrack.Crm.PhoneTimezone.Timer(localValue, timeNode);
                            }
                        }
                    }
                }
            }
        }
    }
}