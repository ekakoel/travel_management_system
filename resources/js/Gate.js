export default class Gate{

    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.type === 'admin';
    }
    isUser(){
        return this.user.type === 'user';
    }
    isAuthor(){
        return this.user.position === 'author';
    }
    isRsv(){
        return this.user.position === 'reservation';
    }
    isDev(){
        return this.user.position === 'developer';
    }
    isAdm(){
        return this.user.position === 'administrator';
    }
    isAgent(){
        return this.user.position === 'agent';
    }
    
    isAdminOrUser(){
        if(this.user.type === 'user' || this.user.type === 'admin' || this.user.position === 'developer' || this.user.position === 'administrator' || this.user.position === 'author' || this.user.position === 'reservation' || this.user.position === 'agent'){
            return true;
        }
    }
}

