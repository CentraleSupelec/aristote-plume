import './styles/app.scss';
import './bootstrap.ts';

import 'bootstrap';
import { registerReactControllerComponents } from '@symfony/ux-react';
import Routing, {RoutingData} from 'fos-router';
import routes from '../var/cache/fosRoutes.json';

Routing.setRoutingData((routes as unknown) as RoutingData);

registerReactControllerComponents(require.context('./react/controllers', true, /\.([jt])sx?$/));
