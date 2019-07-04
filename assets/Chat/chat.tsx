import './chat.scss';
import { h, render } from 'preact';
import App from './App';
import Configuration from './Configuration';

(window as any).Chat = {
    run(rootElement: Element, configuration: Configuration) {
        render(<App configuration={configuration} />, rootElement);
    }
};
