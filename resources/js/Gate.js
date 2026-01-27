export default class Gate{

    constructor(user){
        this.user = user;
    }

    isWeddingRsv(){
        return this.user.position === 'weddingRsv';
    }
    isWeddingDvl(){
        return this.user.position === 'weddingDvl';
    }
    isWeddingSls(){
        return this.user.position === 'weddingSls';
    }
    isWeddingAuthor(){
        return this.user.position === 'weddingAuthor';
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
    isAgent(){
        return this.user.position === 'agent';
    }
    
    isAdminOrUser(){
        if(this.user.type === 'user' || this.user.type === 'admin' || this.user.position === 'developer' || this.user.position === 'author' || this.user.position === 'reservation' || this.user.position === 'weddingRsv' || this.user.position === 'weddingDvl' || this.user.position === 'weddingSls' || this.user.position === 'weddingAuthor' || this.user.position === 'agent'){
            return true;
        }
    }
}

