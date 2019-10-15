<?php defined('BASEPATH') OR exit('No direct script access allowed');

class EventStatus_Query extends Query {

	protected static $Schema = [
		'alias'			=>	'A',
		'index'			=>	null,
		'rollup'		=>	false,
		'rowcount'		=>	true,
		'fields'		=>	[
			'event_id'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.event_id',
				'alias'			=>	'event_id',

				'grouped'		=>	true,
				'groupable'		=>	true,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'type_id'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.type_id',
				'alias'			=>	'type_id',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	false,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'subcategory'	=>	[
				'from'			=>	'B',
				'refs'			=>	'B.extended_title',
				'alias'			=>	'subcategory',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	false,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'slug'	=>	[
				'from'			=>	'B',
				'refs'			=>	'B.slug',
				'alias'			=>	'type_slug',
				'reqs'			=>	['type_id'],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'start_date'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.start_date',
				'alias'			=>	'start_date',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'end_date'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.end_date',
				'alias'			=>	'end_date',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'num_nights'	=>	[
				'from'			=>	true,
				'refs'			=>	'GREATEST(ABS((TO_DAYS(A.start_date) - TO_DAYS(A.end_date))), 1)',
				'alias'			=>	'num_nights',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'published'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.published',
				'alias'			=>	'published',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'featured'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.featured',
				'alias'			=>	'featured',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'available'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.available',
				'alias'			=>	'available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'waiting_list'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.waiting_list',
				'alias'			=>	'waiting_list',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'must_call'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.must_call',
				'alias'			=>	'must_call',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'title'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.title',
				'alias'			=>	'title',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'leadership'		=>	[
				'from'			=>	'S',
				'refs'			=>	'S.headline',
				'alias'			=>	'leadership',
				'selected'		=>	true,
				'orderable'		=>	true
			],
			'application_id'	=>	[
				'from'			=>	'R',
				'refs'			=>	'R.application_id',
				'alias'			=>	'application_id',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'packages'		=>		[
				'from'		=>		'Q',
				'refs'		=>		'Q.packages',
				'alias'		=>		'packages',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'lodging_offered'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.lodging_offered',
				'alias'			=>	'lodging_offered',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'lodging_waiting_list'	=>	[
				'from'			=>	true,
				'refs'			=>	'A.lodging_waiting_list',
				'alias'			=>	'lodging_waiting_list'
			],
			'lodging_waiting_list_available'	=>	[
				'from'			=>	true,
				'refs'			=>	'IF(A.lodging_waiting_list=1,IF(IFNULL( C.waiting_list, 0 ) + IFNULL( D.waiting_list, 0 ) + IFNULL( E.waiting_list, 0 ) + IFNULL( F.waiting_list, 0 ) + IFNULL( C.available, 0 ) + IFNULL( D.available, 0 ) + IFNULL( E.available, 0 ) + IFNULL( F.available, 0 ) > 0, 1, 0 ), 0 )',
				'alias'			=>	'lodging_waiting_list_available',
				'reqs'			=>	[
					'std_waiting',
					'std_available',
					'ste_waiting',
					'ste_available',
					'blc_waiting',
					'blc_available',
					'hcp_waiting',
					'hcp_available',
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'lodging_available'	=>	[
				'from'			=>	true,
				'refs'			=>	'IF( IFNULL( C.available, 0 ) + IFNULL( D.available, 0 ) + IFNULL( E.available, 0 ) + IFNULL( F.available, 0 ) > 0, 1, 0 )',
				'alias'			=>	'lodging_available',
				'reqs'			=>	[
					'std_available',
					'ste_available',
					'blc_available',
					'hcp_available',
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			// Lodging Extras
			'ce_available'	=>	[
				'from'			=>	'G',
				'refs'			=>	'G.available',
				'alias'			=>	'ce_available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'so_available'	=>	[
				'from'			=>	'H',
				'refs'			=>	'H.available',
				'alias'			=>	'so_available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			// Standard Lodging
			'std_id'	=>	[
				'from'			=>	'C',
				'refs'			=>	'C.id',
				'alias'			=>	'std_id',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_available'		=>	[
				'from'			=>	'C',
				'refs'			=>	'C.available',
				'alias'			=>	'std_available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_waiting'		=>	[
				'from'			=>	'C',
				'refs'			=>	'C.waiting_list',
				'alias'			=>	'waiting_list',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_a'		=>	[
				'from'			=>	'I',
				'refs'			=>	'I.active',
				'alias'			=>	'std_a',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_p'		=>	[
				'from'			=>	'I',
				'refs'			=>	'I.pending',
				'alias'			=>	'std_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_p'		=>	[
				'from'			=>	'I',
				'refs'			=>	'I.pending',
				'alias'			=>	'std_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_t'		=>	[
				'from'		=>		'I',
				'refs'		=>		'I.active + I.pending',
				'alias'		=>		'std_t',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_a_ce'	=>	[
				'from'		=>	'I',
				'refs'		=>	'I.active_come_early',
				'alias'		=>	'std_a_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_p_ce'	=>	[
				'from'		=>	'I',
				'refs'		=>	'I.pending_come_early',
				'alias'		=>	'std_p_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_a_so'	=>	[
				'from'		=>	'I',
				'refs'		=>	'I.active_stay_over',
				'alias'		=>	'std_a_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_p_so'	=>	[
				'from'		=>	'I',
				'refs'		=>	'I.pending_stay_over',
				'alias'		=>	'std_p_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'std_t_ce'	=>	[
				'from'		=>	'I',
				'refs'		=>	'I.active_come_early + I.pending_come_early',
				'alias'		=>	'std_t_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			// Suite Lodging
			'ste_id'	=>	[
				'from'			=>	'D',
				'refs'			=>	'D.id',
				'alias'			=>	'ste_id',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_available'		=>	[
				'from'			=>	'D',
				'refs'			=>	'D.available',
				'alias'			=>	'ste_available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_waiting'		=>	[
				'from'			=>	'D',
				'refs'			=>	'D.waiting_list',
				'alias'			=>	'waiting_list',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_a'		=>	[
				'from'			=>	'J',
				'refs'			=>	'J.active',
				'alias'			=>	'ste_a',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_p'		=>	[
				'from'			=>	'J',
				'refs'			=>	'J.pending',
				'alias'			=>	'ste_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_p'		=>	[
				'from'			=>	'J',
				'refs'			=>	'J.pending',
				'alias'			=>	'ste_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_t'		=>	[
				'from'		=>		'J',
				'refs'		=>		'J.active + J.pending',
				'alias'		=>		'ste_t',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_a_ce'	=>	[
				'from'		=>	'J',
				'refs'		=>	'J.active_come_early',
				'alias'		=>	'ste_a_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_p_ce'	=>	[
				'from'		=>	'J',
				'refs'		=>	'J.pending_come_early',
				'alias'		=>	'ste_p_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_a_so'	=>	[
				'from'		=>	'J',
				'refs'		=>	'J.active_stay_over',
				'alias'		=>	'ste_a_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_p_so'	=>	[
				'from'		=>	'J',
				'refs'		=>	'J.pending_stay_over',
				'alias'		=>	'ste_p_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'ste_t_ce'	=>	[
				'from'		=>	'J',
				'refs'		=>	'J.active_come_early + J.pending_come_early',
				'alias'		=>	'ste_t_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			// Balcony Lodging
			'blc_id'	=>	[
				'from'			=>	'E',
				'refs'			=>	'E.id',
				'alias'			=>	'blc_id',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_available'		=>	[
				'from'			=>	'E',
				'refs'			=>	'E.available',
				'alias'			=>	'blc_available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_waiting'		=>	[
				'from'			=>	'E',
				'refs'			=>	'E.waiting_list',
				'alias'			=>	'waiting_list',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_a'		=>	[
				'from'			=>	'K',
				'refs'			=>	'K.active',
				'alias'			=>	'blc_a',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_p'		=>	[
				'from'			=>	'K',
				'refs'			=>	'K.pending',
				'alias'			=>	'blc_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_p'		=>	[
				'from'			=>	'K',
				'refs'			=>	'K.pending',
				'alias'			=>	'blc_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_t'		=>	[
				'from'		=>		'K',
				'refs'		=>		'K.active + K.pending',
				'alias'		=>		'blc_t',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_a_ce'	=>	[
				'from'		=>	'K',
				'refs'		=>	'K.active_come_early',
				'alias'		=>	'blc_a_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_p_ce'	=>	[
				'from'		=>	'K',
				'refs'		=>	'K.pending_come_early',
				'alias'		=>	'blc_p_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_a_so'	=>	[
				'from'		=>	'K',
				'refs'		=>	'K.active_stay_over',
				'alias'		=>	'blc_a_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_p_so'	=>	[
				'from'		=>	'K',
				'refs'		=>	'K.pending_stay_over',
				'alias'		=>	'blc_p_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'blc_t_ce'	=>	[
				'from'		=>	'K',
				'refs'		=>	'K.active_come_early + K.pending_come_early',
				'alias'		=>	'blc_t_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			// Handicap Lodging
			'hcp_id'	=>	[
				'from'			=>	'F',
				'refs'			=>	'F.id',
				'alias'			=>	'hcp_id',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_available'		=>	[
				'from'			=>	'F',
				'refs'			=>	'F.available',
				'alias'			=>	'hcp_available',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_waiting'		=>	[
				'from'			=>	'F',
				'refs'			=>	'F.waiting_list',
				'alias'			=>	'waiting_list',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_a'		=>	[
				'from'			=>	'L',
				'refs'			=>	'L.active',
				'alias'			=>	'hcp_a',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_p'		=>	[
				'from'			=>	'L',
				'refs'			=>	'L.pending',
				'alias'			=>	'hcp_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_p'		=>	[
				'from'			=>	'L',
				'refs'			=>	'L.pending',
				'alias'			=>	'hcp_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_t'		=>	[
				'from'		=>		'L',
				'refs'		=>		'L.active + L.pending',
				'alias'		=>		'hcp_t',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_a_ce'	=>	[
				'from'		=>	'L',
				'refs'		=>	'L.active_come_early',
				'alias'		=>	'hcp_a_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_p_ce'	=>	[
				'from'		=>	'L',
				'refs'		=>	'L.pending_come_early',
				'alias'		=>	'hcp_p_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_a_so'	=>	[
				'from'		=>	'L',
				'refs'		=>	'L.active_stay_over',
				'alias'		=>	'hcp_a_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_p_so'	=>	[
				'from'		=>	'L',
				'refs'		=>	'L.pending_stay_over',
				'alias'		=>	'hcp_p_so',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'hcp_t_ce'	=>	[
				'from'		=>	'L',
				'refs'		=>	'L.active_come_early + L.pending_come_early',
				'alias'		=>	'hcp_t_ce',
				'reqs'		=>		[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'lodging_a'	=>	[
				'from'		=>		true,
				'refs'		=>		'I.active + J.active + K.active + L.active',
				'alias'		=>		'lodging_a',
				'reqs'			=>	[
					'std_a',
					'ste_a',
					'blc_a',
					'hcp_a'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'lodging_p'	=>	[
				'from'		=>		true,
				'refs'		=>		'I.pending + J.pending + K.pending + L.pending',
				'alias'		=>		'lodging_p',
				'reqs'			=>	[
					'std_p',
					'ste_p',
					'blc_p',
					'hcp_p'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'lodging_t'	=>	[
				'from'		=>		true,
				'refs'		=>		'I.active + J.active + K.active + L.active + I.pending + J.pending + K.pending + L.pending',
				'alias'		=>		'lodging_t',
				'reqs'			=>	[
					'std_a',
					'std_p',
					'ste_a',
					'ste_p',
					'blc_a',
					'blc_p',
					'hcp_a',
					'hcp_p'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			't_a_ce'	=>	[
				'from'		=>	true,
				'refs'		=>	'I.active_come_early + J.active_come_early + K.active_come_early + L.active_come_early',
				'alias'		=>	't_a_ce',
				'reqs'			=>	[
					'std_a_ce',
					'ste_a_ce',
					'blc_a_ce',
					'hcp_a_ce'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			't_p_ce'	=>	[
				'from'		=>	true,
				'refs'		=>	'I.pending_come_early + J.pending_come_early + K.pending_come_early + L.pending_come_early',
				'alias'		=>	't_p_ce',
				'reqs'			=>	[
					'std_p_ce',
					'ste_p_ce',
					'blc_p_ce',
					'hcp_p_ce'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			't_ce'	=>	[
				'from'		=>	true,
				'refs'		=>	'I.active_come_early + J.active_come_early + K.active_come_early + L.active_come_early + I.pending_come_early + J.pending_come_early + K.pending_come_early + L.pending_come_early',
				'alias'		=>	't_ce',
				'reqs'			=>	[
					'std_a_ce',
					'std_p_ce',
					'ste_a_ce',
					'ste_p_ce',
					'blc_a_ce',
					'blc_p_ce',
					'hcp_a_ce',
					'hcp_p_ce'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			't_a_so'	=>	[
				'from'		=>	true,
				'refs'		=>	'I.active_stay_over + J.active_stay_over + K.active_stay_over + L.active_stay_over',
				'alias'		=>	't_a_so',
				'reqs'			=>	[
					'std_a_so',
					'ste_a_so',
					'blc_a_so',
					'hcp_a_so'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			't_p_so'	=>	[
				'from'		=>	true,
				'refs'		=>	'I.pending_stay_over + J.pending_stay_over + K.pending_stay_over + L.pending_stay_over',
				'alias'		=>	't_p_so',
				'reqs'			=>	[
					'std_p_so',
					'ste_p_so',
					'blc_p_so',
					'hcp_p_so'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			't_so'	=>	[
				'from'		=>	true,
				'refs'		=>	'I.active_stay_over + J.active_stay_over + K.active_stay_over + L.active_stay_over + I.pending_stay_over + J.pending_stay_over + K.pending_stay_over + L.pending_stay_over',
				'alias'		=>	't_so',
				'reqs'			=>	[
					'std_a_so',
					'std_p_so',
					'ste_a_so',
					'ste_p_so',
					'blc_a_so',
					'blc_p_so',
					'hcp_a_so',
					'hcp_p_so'
				],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'reg_a'	=>	[
				'from'		=>	'M',
				'refs'		=>	'IFNULL( M.total, 0 )',
				'alias'		=>	'reg_a',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'reg_p'	=>	[
				'from'		=>	'N',
				'refs'		=>	'IFNULL( N.total, 0 )',
				'alias'		=>	'reg_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'reg_t'	=>	[
				'from'		=>	'N',
				'refs'		=>	'IFNULL( M.total, 0 ) + IFNULL( N.total, 0 )',
				'alias'		=>	'reg_t',
				'reqs'			=>	['reg_a','reg_p'],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'gst_a'	=>	[
				'from'		=>	'O',
				'refs'		=>	'IFNULL( O.total, 0 )',
				'alias'		=>	'gst_a',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'gst_p'	=>	[
				'from'		=>	'P',
				'refs'		=>	'IFNULL( P.total, 0 )',
				'alias'		=>	'gst_p',
				'reqs'			=>	[],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			],
			'gst_t'	=>	[
				'from'		=>	'O',
				'refs'		=>	'IFNULL( O.total, 0 ) + IFNULL( P.total, 0 )',
				'alias'		=>	'gst_t',
				'reqs'			=>	['gst_a','gst_p'],
				'selected'		=>	true,
				'selectable'	=>	true,
				'deselectable'	=>	true,

				'filters'		=>	[],
				'filterable'	=>	true,

				'grouped'		=>	false,
				'groupable'		=>	false,
				'grouping_idx'	=>	0,

				'orderable'		=>	true
			]
		],
		'joins'			=>	[
			'B'	=>	[
				'JOIN event_types B ON( B.type_id=A.type_id )',
				['type_id']
			],
			'C'	=>	[
				'LEFT JOIN event_lodgings C ON( C.event_id=A.event_id AND C.lodging_id=2 )',
				['event_id']
			],
			'D'	=>	[
				'LEFT JOIN event_lodgings D ON( D.event_id=A.event_id AND D.lodging_id=3 )',
				['event_id']
			],
			'E'	=>	[
				'LEFT JOIN event_lodgings E ON( E.event_id=A.event_id AND E.lodging_id=4 )',
				['event_id']
			],
			'F'	=>	[
				'LEFT JOIN event_lodgings F ON( F.event_id=A.event_id AND F.lodging_id=5 )',
				['event_id']
			],
			'G' =>	[
				'LEFT JOIN v_event_available_addons G ON( G.event_id=A.event_id AND G.type_id=1 )',
				['event_id']
			],
			'H' =>	[
				'LEFT JOIN v_event_available_addons H ON( H.event_id=A.event_id AND H.type_id=2 )',
				['event_id']
			],
			'I' =>	[
				'LEFT JOIN v_registered_event_lodgings_by_type I ON( I.event_id=A.event_id AND I.lodging_id=2 )',
				['event_id']
			],
			'J' =>	[
				'LEFT JOIN v_registered_event_lodgings_by_type J ON( J.event_id=A.event_id AND J.lodging_id=3 )',
				['event_id']
			],
			'K' =>	[
				'LEFT JOIN v_registered_event_lodgings_by_type K ON( K.event_id=A.event_id AND K.lodging_id=4 )',
				['event_id']
			],
			'L' =>	[
				'LEFT JOIN v_registered_event_lodgings_by_type L ON( L.event_id=A.event_id AND L.lodging_id=5 )',
				['event_id']
			],
			'M'	=>	[
				'LEFT JOIN v_registration_totals M ON ( M.event_id=A.event_id AND M.active=1 )',
				['event_id']
			],
			'N'	=>	[
				'LEFT JOIN v_registration_totals N ON ( N.event_id=A.event_id AND N.active=0 )',
				['event_id']
			],
			'O'	=>	[
				'LEFT JOIN v_guest_totals O ON ( O.event_id=A.event_id AND O.active=1 )',
				['event_id']
			],
			'P'	=>	[
				'LEFT JOIN v_guest_totals P ON ( P.event_id=A.event_id AND P.active=0 )',
				['event_id']
			],
			'Q'	=>	[
				'LEFT JOIN v_event_package_totals Q ON( Q.event_id=A.event_id )',
				['event_id']
			],
			'R' =>	[
				'LEFT JOIN event_applications R ON( R.event_id=A.event_id )',
				['event_id']
			],
			'S'	=>	[
				'LEFT JOIN v_event_leaders_compact S ON( S.event_id=A.event_id )',
				['event_id']
			]
			//,
			// 'piwik_outreaches'	=> 	[
			// 	'LEFT JOIN piwik_outreaches B ON(A.outreach_id=B.outreach_id)',
			// 	['outreach_id']
			// ],
			// 'piwik_locations'	=>	[
			// 	'LEFT JOIN piwik_locations C ON(A.location_id=C.location_id)',
			// 	['location_id']
			// ],
			// 'piwik_cities'	=>		[
			// 	'LEFT JOIN piwik_cities D ON(C.city_id=D.city_id)',
			// 	['city_id']
			// ],
			// 'piwik_campaigns'	=>	[
			// 	'LEFT JOIN piwik_campaigns E ON(A.campaign_id=E.campaign_id)',
			// 	['campaign_id']
			// ]
		],
		'from'			=>	'events'
	];


}
