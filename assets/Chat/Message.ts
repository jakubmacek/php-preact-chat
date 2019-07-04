export default interface Message {
    timestamp: number;
    from: number;
    fromName: string;
    to: number;
    private: boolean;
    text: string;
}
