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
    },
    listHandler: function(grid){
        this.list = grid;
        console.log(this.list)
    },
    requestSignals: function(phone) {
        return BX.ajax.runAction('itrack:custom.api.signal.getSignal', {
            data: {
                phone: phone
            }
        });
    },
}