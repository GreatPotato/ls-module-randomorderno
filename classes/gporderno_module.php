<?php
class GPOrderNo_Module extends Core_ModuleBase
{
	/**
	 * Creates the module information object
	 * @return Core_ModuleInfo
	 */
	protected function createModuleInfo()
	{
		return new Core_ModuleInfo(
			'Custom Order No.',
			'Gives an order a randomly generated 5 character order number',
			'GreatPotato',
			'http://www.mrld.co'
		);
	}
	
	
	public function subscribeEvents()
	{
		Backend::$events->addEvent('shop:onExtendOrderModel', $this, 'extend_order_model');
		Backend::$events->addEvent('shop:onExtendOrderForm', $this, 'extend_order_form');
		
		Backend::$events->addEvent('shop:onNewOrder', $this, 'process_new_order');
		Backend::$events->addEvent('backend:onControllerReady', $this, 'on_controller_ready');
		
		Backend::$events->addEvent('backend:onBeforeRenderPartial', $this, 'backend_render_partial');
	}
	
	
	public function extend_order_model($order, $context)
	{
		$order->define_column('x_order_no', 'Order No.');
	}
	
	
	public function extend_order_form($order, $context)
	{
		$order->add_form_field('x_order_no')->tab('Order Details')->sortOrder(1);
	}
	
	
	public function process_new_order($order_id)
	{
		$order = Shop_Order::create()->find($order_id);
		$order->x_order_no = $this->random_string('alnum', 5);
		$order->save();
	}
	
	
	public function on_controller_ready($controller)
	{
		if( get_class($controller) == 'Shop_Orders' )
		{
			$controller->list_search_fields[] = 'x_order_no';
		}
	}
	
	
	
	public function backend_render_partial($controller, $view)
	{
		//echo $view;
		//echo '<li>Test</li>';
	}
	
	
	public function random_string($type = 'alnum', $len = 8)
	{
		switch ($type)
		{
			case 'alpha':
				$pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'alnum':
				$pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'numeric':
				$pool = '0123456789';
				break;
		}
		
		return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
	}
}
	