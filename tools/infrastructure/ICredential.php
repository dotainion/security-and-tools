<?php

namespace tools\infrastructure;

interface ICredential{
    public function id():IId;

	public function pin():PinPassword;

	public function user():?IUser;

	public function token():Token;

	public function expire():?DateHelper;

	public function password():Password;

	public function refreshToken():?string;
}