import Message from './Message';
import User from './User';


export default class AppState {
    public messages: Message[] = [];
    public users: User[] = [];

    public sendMessageText: string = '';
    public sendMessageTo: number = 0;
    public sendMessageToName: string = '';
    public sendMessagePrivate: boolean = false;
}
