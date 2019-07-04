import Message from './Message';
import User from './User';

export default interface ReadMessagesResponse {
    status: string;
    messages: Message[];
    users: User[];
}
