(function (blocks, element, blockEditor, components) {
    var el               = element.createElement;
    var useBlockProps    = blockEditor.useBlockProps;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody        = components.PanelBody;
    var TextControl      = components.TextControl;

    blocks.registerBlockType('whg/health-grader', {
        title:       'Website Health Grader',
        icon:        'chart-bar',
        category:    'widgets',
        description: 'Embed the Website Health Grader tool.',
        attributes: {
            contactUrl: { type: 'string', default: '' }
        },

        edit: function (props) {
            var blockProps = useBlockProps();

            return el(
                'div',
                blockProps,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Settings', initialOpen: true },
                        el(TextControl, {
                            label:    'Contact Page URL (optional)',
                            help:     'Leave empty to use the default Polylang contact page.',
                            value:    props.attributes.contactUrl,
                            onChange: function (val) { props.setAttributes({ contactUrl: val }); }
                        })
                    )
                ),
                el(
                    'div',
                    {
                        style: {
                            padding:      '24px',
                            background:   '#fdf8df',
                            border:       '2px solid #e0bf00',
                            borderRadius: '8px',
                            textAlign:    'center'
                        }
                    },
                    el('strong', null, '\uD83D\uDCCA Website Health Grader'),
                    el('p', { style: { margin: '8px 0 0', color: '#555', fontSize: '14px' } },
                        'The grader widget will render on the front-end.'
                    )
                )
            );
        },

        // Server-side rendered — save returns null
        save: function () { return null; }
    });

}(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components));
