<?php
?>



<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            <form class="login100-form validate-form" method="post">
					<span class="login100-form-title p-b-26">
						Авторизация
					</span>
                <span class="login100-form-title p-b-48">
						<i class="zmdi zmdi-font"></i>
					</span>

                <div class="wrap-input100 validate-input" data-validate = "Valid email is: a@b.c">
                    <input class="input100" type="text" name="login">
                    <span class="focus-input100" data-placeholder="Логин"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
                    <input class="input100" type="password" name="password">
                    <span class="focus-input100" data-placeholder="Пароль"></span>
                </div>

                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn">
                            Войти
                        </button>
                    </div>
                </div>
                <?php
                if(!$matchPass){
                    echo '<div class="text-center p-t-115">
                                            <span class="txt1">
                                                Пароль не верный.
                                            </span>
                                        </div>';
                }

                ?>


            </form>
        </div>
    </div>
</div>


<div id="dropDownSelect1"></div>


