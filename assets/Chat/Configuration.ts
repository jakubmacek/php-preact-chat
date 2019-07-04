interface StringMap {
    [key: string]: string;
}

export default interface Configuration {
    myId: number;
    myName: string;
    smileys: StringMap;
    readMessagesUrl: string;
    sendMessageUrl: string;
}
