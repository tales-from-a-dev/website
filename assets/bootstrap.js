import { startStimulusApp } from '@symfony/stimulus-bundle';
import ScrollTo from '@stimulus-components/scroll-to'

const application = startStimulusApp();

application.register('scroll-to', ScrollTo)
