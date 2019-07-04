//import style from './chat.scss';
import bind from 'bind-decorator';
import { h, Component } from 'preact';
import moment from 'moment';
import 'moment/min/locales';
import AppState from './AppState';
import AppProps from './AppProps';
import User from './User';
import Message from './Message';
import SendMessageResponse from './SendMessageResponse';
import ReadMessagesResponse from './ReadMessagesResponse';

export default class App extends Component<AppProps, AppState> {
	constructor() {
		super();
		this.state = new AppState();
	}

	private sendMessageTextElement: HTMLInputElement | undefined;

	private readMessagesTimer: number | undefined;

	private myNameSearch: string | undefined;

	componentWillMount(): void {
		this.myNameSearch = this.props.configuration.myName + ':';
	}

	componentDidMount(): void {
		this.readMessagesTimer = window.setInterval(() => {
			this.readMessages();
		}, 1000);
	}

	readMessages(): void {
		fetch(this.props.configuration.readMessagesUrl, {
			method: 'GET',
			cache: 'no-cache'
		}).then(response => {
			return response.json();
		})
		.then((data: ReadMessagesResponse) => {
			this.setState({
				messages: data.messages,
				users: data.users
			});
		});
	}

	clearSendMessageText(): void {
		this.setState(() => ({
			sendMessageText: ''
		}));
		this.sendMessageTextElement!.value = '';
	}

	@bind
	sendMessage(): void {
		if (!this.state.sendMessageText)
			return;

		let sendData = {
			private: this.state.sendMessagePrivate,
			to: this.state.sendMessageTo,
			text: this.state.sendMessageText
		};
		this.clearSendMessageText();

		fetch(this.props.configuration.sendMessageUrl, {
			method: 'POST',
			cache: 'no-cache',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(sendData)
		}).then(response => {
			return response.json();
		})
		.then((/*data: SendMessageResponse*/) => {
			this.readMessages();
		});
	}

	setSendMessageTextFromElement(): void {
		this.setState(() => ({
			sendMessageText: this.sendMessageTextElement!.value
		}));
	}

	@bind
	updateSendMessageText(ev: KeyboardEvent): void {
		if (ev.keyCode === 13) {
			this.sendMessage();
		} else {
			this.setSendMessageTextFromElement();
		}
	}

	@bind
	setSendMessageToPublic(user: User): void {
		this.setState(() => ({
			sendMessageTo: user.id,
			sendMessageToName: user.name,
			sendMessagePrivate: false
		}));
	}

	@bind
	setSendMessageToPrivate(user: User): void {
		this.setState(() => ({
			sendMessageTo: user.id,
			sendMessageToName: user.name,
			sendMessagePrivate: true
		}));
	}

	@bind
	unsetSendMessageTo(): void {
		this.setState(() => ({
			sendMessageTo: 0,
			sendMessageToName: '',
			sendMessagePrivate: false
		}));
	}

	@bind addSmiley(symbol: string): void {
		this.sendMessageTextElement!.value = this.sendMessageTextElement!.value + symbol + ' ';
		this.setSendMessageTextFromElement();
	}

	static formatTimestamp(timestamp: number): string {
		return moment(timestamp).locale('cs').format('LTS');
	}

	renderSmileys() {
		return <span>
			{Object.entries(this.props.configuration.smileys).map(([symbol, imageSrc]) => <img class="smiley" alt="" src={imageSrc} onClick={() => this.addSmiley(symbol)} />)}
		</span>;
	}

	renderSendMessageTo() {
		if (this.state.sendMessageTo) {
			return <div class="alert alert-warning alert-dismissiblexxx" role="alert">
				<strong>Komu:</strong> {this.state.sendMessageToName}
				{this.state.sendMessagePrivate ? <span class="badge badge-danger">soukromě</span> : <span class="badge badge-warning">veřejně</span>}
				<button type="button" class="close" onClick={this.unsetSendMessageTo}>
					<span aria-hidden="true">&times;</span>
				</button>
			</div>;
		} else {
			return <span/>;
		}
	}

	renderSendMessageBlock() {
		return <div>
			<div class="row">
				<div class="col-7">
					<input class="form-control" type="text" ref={e => this.sendMessageTextElement = e} onKeyUp={this.updateSendMessageText}/>
				</div>
				<div class="col-1">
					<button class="btn btn-primary btn-sm" onClick={this.sendMessage}>Odeslat</button>
				</div>
				<div class="col-4">
					{this.renderSendMessageTo()}
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					{this.renderSmileys()}
				</div>
			</div>
		</div>;
	}

	renderUsers() {
		return <div class="card shadow-sm">
			<div class="card-header">Uživatelé</div>
			<div class="card-body">
				<ul class="list-unstyled">
					{this.state.users.map(user => <div>
						<li class={'user user--sex-' + user.sex}><a href="#" onClick={() => this.setSendMessageToPublic(user)} onDblClick={() => this.setSendMessageToPrivate(user)}>{user.name}</a></li>
					</div>)}
				</ul>
			</div>
		</div>;
	}

	getMessageClass(message: Message): string {
		let className = 'message';
		if (message.from == 0) {
			className += ' message--from-system';
		}
		if (message.private) {
			className += ' message--private';
		}
		if (message.from == this.props.configuration.myId) {
			className += ' message--from-me';
		}
		if (message.text.indexOf(this.myNameSearch!) >= 0) {
			className += ' message--to-me';
		}
		return className;
	}

	renderMessages() {
		return <div>
			{this.state.messages.map(message => <div class={this.getMessageClass(message)}>
				<span class="message__timestamp">{App.formatTimestamp(message.timestamp)}</span>
				<span class="message__from">{message.fromName}:</span>
				<span class="message__text" dangerouslySetInnerHTML={{__html: message.text}}/>
			</div>)}
		</div>;
	}

	render() {
		return <div>
			<div class="row">
				<div class="col-9">
					{this.renderSendMessageBlock()}
					<br/>
					{this.renderMessages()}
				</div>
				<div class="col-3">
					{this.renderUsers()}
				</div>
			</div>
		</div>;
	}
}
