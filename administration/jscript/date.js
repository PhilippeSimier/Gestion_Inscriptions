// donne le quanti�me d'une date revoie un entier de 1 � 366
Date.prototype.getYearDay = function() {
	var year  = this.getFullYear();    // l'ann�e compl�te sur 4 chiffres
	var month = this.getMonth();       // donne le mois (0 pour janvier  11 pour d�cembre)
	var day   = this.getDate();        // donne le jour du mois (0� 31)

	var offset = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
	
	//l'ann�e bissextile n'est utile qu'� partir du mois de mars
	var bissextile = (month < 2) ? 0 : (year % 400 == 0 || (year % 4 == 0 && year % 100 != 0));
	
    return parseInt(day + offset[month] + bissextile);
}

// donne une date correspondant au lundi d'une date
Date.prototype.getMonday = function() {
	var offset = (this.getDay() + 6) % 7;
	return new Date(this.getFullYear(), this.getMonth(), this.getDate()-offset);
}

// donne le num�ro ISO de la semaine
Date.prototype.getWeek = function() { //1 - 53
	var year = this.getFullYear();
	var week;
	
	//dernier lundi de l'ann�e
	var lastMonday = new Date(year, 11, 31).getMonday();
	
	//la date est dans la derni�re semaine de l'ann�e
	//mais cette semaine fait partie de l'ann�e suivante
	if(this >= lastMonday && lastMonday.getDate() > 28) {
		week = 1;
	}
	else {
		//premier lundi de l'ann�e
		var firstMonday = new Date(year, 0, 1).getMonday();
		
		//correction si n�cessaire (le lundi se situe l'ann�e pr�c�dente)
		if(firstMonday.getFullYear() < year) firstMonday = new Date(year, 0, 8).getMonday();
		
		//nombre de jours �coul�s depuis le premier lundi
		var days = this.getYearDay() - firstMonday.getYearDay();
		
		//window.alert(days);
		
		//si le nombre de jours est n�gatif on va chercher
		//la derni�re semaine de l'ann�e pr�c�dente (52 ou 53)
		if(days < 0) {
			week = new Date(year, this.getMonth(), this.getDate()+days).getWeek();
		}
		else {
			//num�ro de la semaine
			week = 1 + parseInt(days / 7);
			
			//on ajoute une semaine si la premi�re semaine
			//de l'ann�e ne fait pas partie de l'ann�e pr�c�dente
			week += (new Date(year-1, 11, 31).getMonday().getDate() > 28);
		}
	}
	
	return parseInt(week);
}

