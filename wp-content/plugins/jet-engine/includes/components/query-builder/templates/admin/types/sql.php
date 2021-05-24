<?php
/**
 * Posts query component template
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php _e( 'Custom SQL Query', 'jet-engine' ); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-select
			label="<?php _e( 'From table', 'jet-engine' ); ?>"
			description="<?php _e( 'Select data from the given table', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="tablesList"
			size="fullwidth"
			v-model="query.table"
		></cx-vui-select>
		<cx-vui-switcher
			label="<?php _e( 'Use Join', 'jet-engine' ); ?>"
			description="<?php _e( 'Join data from other DB tables.', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			name="query_use_join"
			v-model="query.use_join"
		></cx-vui-switcher>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
			v-if="query.use_join"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Join Tables', 'jet-engine' ); ?></div>
				<div class="cx-vui-component__desc"><?php _e( '<b>Note:</b> If you planning to use multiple joins by same table, you need to define queried columns with <b>Include columns</b>. Without this some data may be lost in the returned object.', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.join_tables"
					@add-new-item="addNewField( $event, [], query.join_tables )"
				>
					<cx-vui-repeater-item
						v-for="( clause, index ) in query.join_tables"
						:collapsed="isCollapsed( clause )"
						:index="index"
						@clone-item="cloneField( $event, clause._id, query.join_tables )"
						@delete-item="deleteField( $event, clause._id, query.join_tables )"
						:key="clause._id"
					>
						<cx-vui-select
							label="<?php _e( 'Join Type', 'jet-engine' ); ?>"
							description="<?php _e( 'Select join type. If not set will be used Inner Join', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="[
								{
									value: 'INNER JOIN',
									label: 'Inner Join',
								},
								{
									value: 'LEFT JOIN',
									label: 'Left Join',
								},
								{
									value: 'RIGHT JOIN',
									label: 'Right Join',
								},
								{
									value: 'OUTER JOIN',
									label: 'Outer Join',
								},
							]"
							size="fullwidth"
							:value="query.join_tables[ index ].type"
							@input="setFieldProp( clause._id, 'type', $event, query.join_tables )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Join Table', 'jet-engine' ); ?>"
							description="<?php _e( 'Select DB table to get joined data from', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="tablesList"
							size="fullwidth"
							:value="query.join_tables[ index ].table"
							@input="setFieldProp( clause._id, 'table', $event, query.join_tables )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'When current table column', 'jet-engine' ); ?>"
							description="<?php _e( 'Select column from the current table to find match between two tables on value of this columns', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="getColumns( query.join_tables[ index ].table )"
							size="fullwidth"
							:value="query.join_tables[ index ].on_current"
							@input="setFieldProp( clause._id, 'on_current', $event, query.join_tables )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Is equal to base table column', 'jet-engine' ); ?>"
							description="<?php _e( 'Select columns from the initial table to find match between two tables on value of this columns', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="getColumns( query.table )"
							size="fullwidth"
							:value="query.join_tables[ index ].on_base"
							@input="setFieldProp( clause._id, 'on_base', $event, query.join_tables )"
						></cx-vui-select>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Where (query clauses)', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.where"
					@add-new-item="addNewField( $event, [], query.where, newDynamicWhere )"
				>
					<cx-vui-repeater-item
						v-for="( whereClause, index ) in query.where"
						:collapsed="isCollapsed( whereClause )"
						:index="index"
						@clone-item="cloneField( $event, whereClause._id, query.where, newDynamicWhere )"
						@delete-item="deleteField( $event, whereClause._id, query.where, deleteDynamicWhere )"
						:key="whereClause._id"
					>
						<cx-vui-select
							label="<?php _e( 'Column', 'jet-engine' ); ?>"
							description="<?php _e( 'Select column to query results by', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="availableColumns"
							size="fullwidth"
							:value="query.where[ index ].column"
							@input="setFieldProp( whereClause._id, 'column', $event, query.where )"
						></cx-vui-select>
						<cx-vui-input
							label="<?php _e( 'Value', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth', 'has-macros' ]"
							size="fullwidth"
							:value="query.where[ index ].value"
							@input="setFieldProp( whereClause._id, 'value', $event, query.where )"
						><jet-query-dynamic-args v-model="dynamicQuery.where[ whereClause._id ].value"></jet-query-dynamic-args></cx-vui-input>
						<cx-vui-select
							label="<?php _e( 'Compare', 'jet-engine' ); ?>"
							description="<?php _e( 'Operator to test', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="operators"
							size="fullwidth"
							:value="query.where[ index ].compare"
							@input="setFieldProp( whereClause._id, 'compare', $event, query.where )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Type', 'jet-engine' ); ?>"
							description="<?php _e( 'Data type stored in the given column', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="dataTypes"
							size="fullwidth"
							:value="query.where[ index ].type"
							@input="setFieldProp( whereClause._id, 'type', $event, query.where )"
						></cx-vui-select>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'fullwidth-control' ]"
		>
			<div class="cx-vui-inner-panel query-panel">
				<div class="cx-vui-component__label"><?php _e( 'Order & Order By', 'jet-engine' ); ?></div>
				<cx-vui-repeater
					button-label="<?php _e( 'Add new sorting parameter', 'jet-engine' ); ?>"
					button-style="accent"
					button-size="mini"
					v-model="query.orderby"
					@add-new-item="addNewField( $event, [], query.orderby )"
				>
					<cx-vui-repeater-item
						v-for="( order, index ) in query.orderby"
						:title="query.orderby[ index ].orderby"
						:subtitle="query.orderby[ index ].order"
						:collapsed="isCollapsed( order )"
						:index="index"
						@clone-item="cloneField( $event, order._id, query.orderby )"
						@delete-item="deleteField( $event, order._id, query.orderby )"
						:key="order._id"
					>
						<cx-vui-select
							label="<?php _e( 'Order By', 'jet-engine' ); ?>"
							description="<?php _e( 'Sort retrieved items by selected parameter', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="availableColumns"
							size="fullwidth"
							:value="query.orderby[ index ].orderby"
							@input="setFieldProp( order._id, 'orderby', $event, query.orderby )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Order', 'jet-engine' ); ?>"
							description="<?php _e( 'Designates the ascending or descending order of the `Order By` parameter', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="[
								{
									value: 'ASC',
									label: 'From lowest to highest values (1, 2, 3; a, b, c)',
								},
								{
									value: 'DESC',
									label: 'From highest to lowest values (3, 2, 1; c, b, a)',
								},
							]"
							size="fullwidth"
							:value="query.orderby[ index ].order"
							@input="setFieldProp( order._id, 'order', $event, query.orderby )"
						></cx-vui-select>
						<cx-vui-select
							label="<?php _e( 'Type', 'jet-engine' ); ?>"
							description="<?php _e( 'Data type stored in the given column', 'jet-engine' ); ?>"
							:wrapper-css="[ 'equalwidth' ]"
							:options-list="dataTypes"
							size="fullwidth"
							:value="query.orderby[ index ].type"
							@input="setFieldProp( order._id, 'type', $event, query.orderby )"
						></cx-vui-select>
					</cx-vui-repeater-item>
				</cx-vui-repeater>
			</div>
		</cx-vui-component-wrapper>
		<cx-vui-input
			label="<?php _e( 'Show/Per Page Limit', 'jet-engine' ); ?>"
			description="<?php _e( 'If using with JetSmartFilters pagination - its number of returned items per page. If without pagination - its number of visible items in the listing grid. To count all possible results count, but show only N items in the grid - set N into this option and leave empty <b>Total Query Limit</b>', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			v-model="query.limit_per_page"
		><jet-query-dynamic-args v-model="dynamicQuery.limit_per_page"></jet-query-dynamic-args></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Total Query Limit', 'jet-engine' ); ?>"
			description="<?php _e( 'Maximum allowed number of queried items. If using with JetSmartFilters pagination - defines total number of found items. If without pagination - its a total number that will be shown in the Query Count dynamic tag, Listings Grid still will show number of items set in the <b>Show/Per Page Limit</b> option. To count all possible results count, but show only N items in the grid - leave this option empty and set N into <b>Show/Per Page Limit</b> option', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			v-model="query.limit"
		><jet-query-dynamic-args v-model="dynamicQuery.limit"></jet-query-dynamic-args></cx-vui-input>
		<cx-vui-input
			label="<?php _e( 'Offset', 'jet-engine' ); ?>"
			description="<?php _e( 'Number of items to skip from start', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'has-macros' ]"
			size="fullwidth"
			v-model="query.offset"
		><jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args></cx-vui-input>
		<cx-vui-f-select
			label="<?php _e( 'Include columns', 'jet-engine' ); ?>"
			name="query_post_type"
			description="<?php _e( 'Select what columns should be included into the query results. Leave empty to return all possible columns', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="availableColumns"
			:autocomplete="'autocomplete_' + randID()"
			size="fullwidth"
			:multiple="true"
			v-model="query.include_columns"
		></cx-vui-f-select>
		<cx-vui-component-wrapper
			:wrapper-css="[ 'equalwidth' ]"
			label="<?php _e( 'Columns for filters', 'jet-engine' ); ?>"
			description="<?php _e( 'Available columns list to filter with <b>JetSmartFilters</b> plugin. To filter query results by selected column, copy column name and paste it into <b>Query Variable</b> option of selected filter.', 'jet-engine' ); ?>"
		>
			<div>
				<code v-for="column in availableColumns" :style="{ display: 'inline-block', marginBottom: '2px' }">{{ column.label }}</code>
			</div>
		</cx-vui-component-wrapper>
	</div>
</div>
