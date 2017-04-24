<?php
namespace Skeleton\CMB2;

use Skeleton\Support\Priority_List;

class Tabable_Stack extends Priority_List {
	/**
	 * Make a list stack
	 *
	 * @param  array $values An array values.
	 * @return static
	 */
	public static function make( array $values ) {
		$stack = new static;

		foreach ( $values as $key => $value ) {
			$priority = is_object( $value ) ? $value->priority : $value['priority'];
			$stack->insert( $key, $value, $priority );
		}

		return $stack;
	}

	/**
	 * Compare the priority of two items.
	 *
	 * @param  array $item1
	 * @param  array $item2
	 * @return int
	 */
	protected function compare( array $item1, array $item2 ) {
		return ( $item1['priority'] === $item2['priority'] )
			? ( $item1['serial']   > $item2['serial']   ? 1 : -1 ) * $this->isLIFO
			: ( $item1['priority'] > $item2['priority'] ? 1 : -1 );
	}
}
