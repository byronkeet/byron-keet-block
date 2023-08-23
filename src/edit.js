import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, Panel, PanelBody } from '@wordpress/components';

import './editor.scss';

export default function Edit(props) {
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;
	const { showID, showFirstName, showLastName, showEmail, showDate } = attributes;
	const [data, setData] = useState(null);

	useEffect( () => {
		fetch( wp.ajax.settings.url + '?action=byron_keet_fetch_miusage_data' )
		.then( response => response.json() )
		.then( result => {
			if ( result.success ) {
				setData( JSON.parse(result.data));
			}
		});
	}, [] );

	if (!data || !data.data) {
		return null;
	}

	const rows = Object.values(data.data.rows || {});

	return (
		<div>
			<InspectorControls>
				<Panel header="My Panel">
					<PanelBody title={__('Show column', 'byron-keet')}>
						<ToggleControl
							label="Show ID"
							checked={showID}
							onChange={() => setAttributes({ showID: !showID })}
						/>
						<ToggleControl
							label="Show First Name"
							checked={showFirstName}
							onChange={() => setAttributes({ showFirstName: !showFirstName })}
						/>
						<ToggleControl
							label="Show Last Name"
							checked={showLastName}
							onChange={() => setAttributes({ showLastName: !showLastName })}
						/>
						<ToggleControl
							label="Show Email"
							checked={showEmail}
							onChange={() => setAttributes({ showEmail: !showEmail })}
						/>
						<ToggleControl
							label="Show Date"
							checked={showDate}
							onChange={() => setAttributes({ showDate: !showDate })}
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>
			
			<table { ...blockProps }>
				<thead>
					<tr>
						{showID && <th>ID</th>}
						{showFirstName && <th>First Name</th>}
						{showLastName && <th>Last Name</th>}
						{showEmail && <th>Email</th>}
						{showDate && <th>Date</th>}
					</tr>
				</thead>
				<tbody>
					{rows.map(row => (
						<tr key={row.id}>
							{showID && <td>{row.id}</td>}
							{showFirstName && <td>{row.fname}</td>}
							{showLastName && <td>{row.lname}</td>}
							{showEmail && <td>{row.email}</td>}
							{showDate && <td>{new Date(row.date * 1000).toLocaleDateString()}</td>}
						</tr>
					))}
				</tbody>
			</table>
		</div>
	);
}

