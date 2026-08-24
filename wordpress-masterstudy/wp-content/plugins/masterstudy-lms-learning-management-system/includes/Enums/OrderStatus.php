<?php


namespace MasterStudy\Lms\Enums;

final class OrderStatus extends Enum {
	public const PENDING   = 'pending';
	public const COMPLETED = 'completed';
	public const CANCELLED = 'cancelled';
}
