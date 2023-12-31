    const { registerBlockType } = wp.blocks;
    const { useSelect } = wp.data;
    const { CheckboxControl, RadioControl, PanelBody } = wp.components;
    const { InspectorControls } = wp.blockEditor;

    registerBlockType('wp-roadmap-pro/roadmap-block', {
        title: 'Roadmap Block',
        category: 'common',
        attributes: {
            selectedStatuses: {
                type: 'object',
                default: {},
            },
            statusFilter: { // New attribute for status filter
                type: 'string',
                default: 'published', // Default to show only published ideas
            },
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const statuses = useSelect(select => {
                return select('core').getEntityRecords('taxonomy', 'status', { per_page: -1 });
            }, []);
        
            const updateSelectedStatuses = (termSlug, isChecked) => {
                const newStatuses = { ...attributes.selectedStatuses, [termSlug]: isChecked };
                setAttributes({ selectedStatuses: newStatuses });
            };
        
            return (
                <div>
                    <InspectorControls>
                        <PanelBody title="Select Statuses">
                            {statuses && statuses.map(term => (
                                <CheckboxControl
                                    label={term.name}
                                    checked={!!attributes.selectedStatuses[term.slug]}
                                    onChange={(isChecked) => updateSelectedStatuses(term.slug, isChecked)}
                                />
                            ))}
                            <PanelBody title="Status Filter">
                                <RadioControl
                                    label="Idea Status"
                                    selected={attributes.statusFilter}
                                    options={[
                                        { label: 'Show only published ideas', value: 'published' },
                                        { label: 'Include ideas pending review', value: 'include_pending' },
                                    ]}
                                    onChange={(value) => setAttributes({ statusFilter: value })}
                                />
                            </PanelBody>
                        </PanelBody>
                    </InspectorControls>
                    <p>Roadmap Block Preview</p>
                </div>
            );
        },
        
        save: function() {
            return null; // Render in PHP
        }
    });


