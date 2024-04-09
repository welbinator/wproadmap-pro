import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl, RadioControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';


/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	// const { attributes, setAttributes } = props;
            const statuses = useSelect(select => {
                return select('core').getEntityRecords('taxonomy', 'idea-status', { per_page: -1 });
            }, []);
        
            const updateSelectedStatuses = (termSlug, isChecked) => {
                const newStatuses = { ...attributes.selectedStatuses, [termSlug]: isChecked };
                setAttributes({ selectedStatuses: newStatuses });
            };
        
            // hook to fetch courses.
    const courses = useSelect((select) => {
        return select('core').getEntityRecords('postType', 'sfwd-courses', { per_page: -1 });
    }, []);

    // Function to handle course checkbox change
    const onCourseCheckboxChange = (courseId, isChecked) => {
        // Ensure selectedCourses is an array, fallback to empty array if not
        const currentSelectedCourses = Array.isArray(attributes.selectedCourses) ? attributes.selectedCourses : [];
        
        let updatedSelectedCourses;
        if (isChecked) {
            // Add the courseId to the array if it's checked and not already present
            updatedSelectedCourses = [...currentSelectedCourses, courseId];
        } else {
            // Remove the courseId from the array if it's unchecked
            updatedSelectedCourses = currentSelectedCourses.filter(id => id !== courseId);
        }
    
        // Update the block's attributes with the new array of selected course IDs
        setAttributes({ selectedCourses: updatedSelectedCourses });
    };

            return (
                <div {...useBlockProps()}>
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
                        <PanelBody title="Access Control">
                        <CheckboxControl
                            label="Allow only logged in users to see this block?"
                            checked={attributes.onlyLoggedInUsers}
                            onChange={(isChecked) => setAttributes({ onlyLoggedInUsers: isChecked })}
                        />
                    </PanelBody>
                    {window.learndashIsActive && window.learndashIsActive.active && (
                    <PanelBody title={__("Allow only students enrolled in the following courses to see this block:", "roadmapwp-pro")}>
                        {courses && courses.map(course => (
                            <CheckboxControl
                                key={course.id}
                                label={course.title.rendered}
                                checked={attributes.selectedCourses ? attributes.selectedCourses.includes(course.id) : false}
                                onChange={(isChecked) => onCourseCheckboxChange(course.id, isChecked)}
                            />
                        ))}
                    </PanelBody>
                )}
                    </InspectorControls>
                    <p>Roadmap Block</p>
                </div>
            );
}
