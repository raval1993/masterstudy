<?php

namespace MasterStudy\Lms\Enums;

final class LessonAudioType extends Enum {
	public const EMBED     = 'embed';
	public const EXT_LINK  = 'ext_link';
	public const FILE      = 'file';
	public const SHORTCODE = 'shortcode';
}
