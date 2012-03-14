/* make a local <select>/combo box */
// create the data store
/*
var YesNoData = new Ext.data.SimpleStore({
    fields: [
       {name: 'id'},
       {name: 'name'}
    ]
});
var myData = [
    [1, _('yes')],
    [0, _('no')]
];
YesNoData.loadData(myData);

Cmp.combo.YesNo = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        displayField: 'name'
        ,valueField: 'id'
        ,fields: ['id', 'name']
        ,store: myData // [{id: 1, name: _('yes')}, {id: 0, name: _('no')} ]
        //,url: Testapp.config.connectorUrl
        ,baseParams: { action: '' ,combo: true }
        ,mode: 'local'
        ,editable: false
    });
    Cmp.combo.YesNo.superclass.constructor.call(this,config);
};
//Ext.extend(MODx.combo.SlideStatus, MODx.combo.ComboBox);
Ext.extend(Cmp.combo.YesNo,MODx.combo.ComboBox);
Ext.reg('yesno-combo', Cmp.combo.YesNo);
*/

Cmp.grid.CmpGenerator = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'cmp-grid-cmpgenerator'
        ,url: Cmp.config.connectorUrl
        ,baseParams: { action: 'mgr/cmpgenerator/getList' }
        ,save_action: 'mgr/cmpgenerator/updateFromGrid'
        ,fields: ['id','package', 'database', 'tables', 'table_prefix','build_scheme', 'build_package','create_date','last_ran']
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'tables'
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 30
        },{
            header: _('cmpgenerator.package')
            ,dataIndex: 'package'
            ,sortable: true
            ,width: 40
            ,editor: { xtype: 'textfield' }
        },{
            header: _('cmpgenerator.database')
            ,dataIndex: 'database'
            ,sortable: true
            ,width: 40
            ,editor: { xtype: 'textfield' }
        },{
            header: _('cmpgenerator.tables')
            ,dataIndex: 'tables'
            ,sortable: false
            ,width: 100
            ,editor: { xtype: 'textarea' } 
        },{
            header: _('cmpgenerator.table_prefix')
            ,dataIndex: 'table_prefix'
            ,sortable: true
            ,width: 20
            ,editor: { xtype: 'textfield' }
        },{
            header: _('cmpgenerator.build_scheme')
            ,dataIndex: 'build_scheme'
            ,sortable: true
            ,width: 20
            //,editor: { xtype: 'combo-boolean' ,renderer: 'boolean' }
            ,renderer: this.renderYNfield.createDelegate(this,[this],true)
            ,editor: { xtype: 'combo-boolean' }
        },{
            header: _('cmpgenerator.build_package')
            ,dataIndex: 'build_package'
            ,sortable: true
            ,width: 20
            //,editor: { xtype: 'combo-boolean' ,renderer: 'boolean' }
            ,renderer: this.renderYNfield.createDelegate(this,[this],true)
            ,editor: { xtype: 'combo-boolean' }
        },{
            header: _('cmpgenerator.create_date')
            ,dataIndex: 'create_date'
            ,sortable: true
            ,width: 40
        },{
            header: _('cmpgenerator.last_ran')
            ,dataIndex: 'last_ran'
            ,sortable: true
            ,width: 40
        }]
        ,tbar: [{
            xtype: 'textfield'
            ,id: 'cmp-search-filter'
            ,emptyText: _('cmpgenerator.search...')
            ,listeners: {
                'change': {fn:this.search,scope:this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this);
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        },{
            text: _('cmpgenerator.create')
            ,handler: { xtype: 'cmp-window-cmpgenerator-build' ,blankValues: true }
        }]
    });
    
    Cmp.grid.CmpGenerator.superclass.constructor.call(this,config);
    
};

Ext.extend(Cmp.grid.CmpGenerator,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function() {
        
        var m = [{
            text: _('cmpgenerator.build')
            ,handler: this.build
        },/*{
            text: _('cmpgenerator.update_schema')
            ,handler: this.updateSchema
        },{
            // Remap (rebuild the map.inc ) from Schema
            text: _('cmpgenerator.remap')
            ,handler: this.remap
        },{
            text: _('cmpgenerator.regenerate_code')
            ,handler: this.regenerateCode
        },*/'-',{
            text: _('cmpgenerator.remove')
            ,handler: this.removeCmp
        }];
        this.addContextMenuItem(m);
        
        return true;
    }
    ,build: function(btn,e) {
        //console.log('Update');
        if (!this.buildWindow) {
            this.buildWindow = MODx.load({
                xtype: 'cmp-window-cmpgenerator-build'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        } else {
            this.buildWindow.setValues(this.menu.record);
        }
        this.buildWindow.show(e.target);
    }

    ,removeCmp: function() {
        MODx.msg.confirm({
            title: _('cmpgenerator.remove')
            ,text: _('cmpgenerator.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/cmpgenerator/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    },
    renderYNfield: function(v,md,rec,ri,ci,s,g) {
        var r = s.getAt(ri).data;
        v = Ext.util.Format.htmlEncode(v);
        var f = MODx.grid.Grid.prototype.rendYesNo;
        return f(v,md,rec,ri,ci,s,g);
    }
});
Ext.reg('cmp-grid-cmpgenerator',Cmp.grid.CmpGenerator);

Cmp.window.BuildCmp = function(config) {
    //console.log('Update');
    config = config || {};
    Ext.applyIf(config,{
        title: _('cmpgenerator.build')
        ,url: Cmp.config.connectorUrl
        ,baseParams: {
            action: 'mgr/cmpgenerator/build'
        }
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('cmpgenerator.package')
            ,name: 'package'
            ,width: 300
            ,disable: true
            ,editable: false
        },{
            xtype: 'textfield'
            ,fieldLabel: _('cmpgenerator.database')
            ,name: 'database'
            ,width: 300
            ,disable: true
            ,editable: false
        },{
            //xtype: 'textfield'
            xtype: 'textarea'
            ,renderer: true
            ,fieldLabel: _('cmpgenerator.tables')
            ,name: 'tables'
            ,width: 300
        },{
            xtype: 'textfield'
            ,fieldLabel: _('cmpgenerator.table_prefix')
            ,name: 'table_prefix'
            ,width: 150
            ,readonly: true
            ,disable: true
            ,editable: false
        },{
            fieldLabel: _('cmpgenerator.build_scheme')
            ,name: 'build_scheme'
            ,width: 100
            ,'default': 1
            ,xtype: 'combo-boolean' //'yesno-combo'// 
            ,renderer: 'boolean'
        },{
            fieldLabel: _('cmpgenerator.build_package')
            ,name: 'build_package'
            ,width: 100
            ,'default': 1
            ,xtype: 'combo-boolean'
            ,renderer: 'boolean'
        }]
    });
    Cmp.window.BuildCmp.superclass.constructor.call(this,config);
    //console.log('Build Assign');
};
Ext.extend(Cmp.window.BuildCmp,MODx.Window);
Ext.reg('cmp-window-cmpgenerator-build',Cmp.window.BuildCmp);
